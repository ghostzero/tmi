<?php

namespace GhostZero\Tmi;

use GhostZero\Tmi\Events\EventHandler;
use GhostZero\Tmi\Events\EventSub\ChatMessageEvent;
use GhostZero\Tmi\Events\EventSub\ChatNotificationEvent;
use Ratchet\Client\WebSocket;
use Ratchet\RFC6455\Messaging\MessageInterface;
use React\EventLoop\LoopInterface;
use React\Http\Browser;

class EventSubClient
{
    private const WEBSOCKET_URL = 'wss://eventsub.wss.twitch.tv/ws';
    private const API_SUBSCRIPTIONS_URL = 'https://api.twitch.tv/helix/eventsub/subscriptions';
    private const API_CHAT_MESSAGES_URL = 'https://api.twitch.tv/helix/chat/messages';

    private EventSubOptions $options;
    private LoopInterface $loop;
    private EventHandler $eventHandler;
    private Browser $browser;
    private array $commands = [];

    public function __construct(EventSubOptions $options)
    {
        $this->options = $options;
        $this->loop = \React\EventLoop\Factory::create();
        $this->eventHandler = new EventHandler();
        $this->browser = new Browser($this->loop);
    }

    public function connect(): void
    {
        $this->connectWebSocket(self::WEBSOCKET_URL);
        $this->loop->run();
    }

    public function on(string $event, callable $closure): self
    {
        $this->eventHandler->addHandler($event, $closure);
        return $this;
    }

    public function any(callable $closure): self
    {
        return $this->on('*', $closure);
    }

    /**
     * Registers a chat command handler. The command name must include the prefix (e.g. '!hello').
     * The handler receives a CommandContext with reply() and say() helpers.
     */
    public function command(string $name, callable $handler): self
    {
        $this->commands[strtolower($name)] = $handler;
        return $this;
    }

    /**
     * Sends a chat message via the Helix API. Pass a replyToMessageId to create a Twitch reply thread.
     */
    public function sendMessage(string $broadcasterUserId, string $message, ?string $replyToMessageId = null): void
    {
        $body = [
            'broadcaster_id' => $broadcasterUserId,
            'sender_id' => $this->options->getBotUserId(),
            'message' => $message,
        ];

        if ($replyToMessageId !== null) {
            $body['reply_parent_message_id'] = $replyToMessageId;
        }

        $this->browser->post(self::API_CHAT_MESSAGES_URL, [
            'Authorization' => 'Bearer ' . $this->options->getAccessToken(),
            'Client-Id' => $this->options->getClientId(),
            'Content-Type' => 'application/json',
        ], json_encode($body))->then(
            function (\Psr\Http\Message\ResponseInterface $response) {
                $this->debug("Chat message sent: HTTP {$response->getStatusCode()}");
            },
            function (\Exception $e) {
                $this->debug("Failed to send chat message: " . $e->getMessage());
            }
        );
    }

    public function getLoop(): LoopInterface
    {
        return $this->loop;
    }

    public function getEventHandler(): EventHandler
    {
        return $this->eventHandler;
    }

    private function connectWebSocket(string $url): void
    {
        \Ratchet\Client\connect($url, [], [], $this->loop)->then(
            function (WebSocket $conn) {
                $this->debug("Connected to EventSub WebSocket.");

                $conn->on('message', function (MessageInterface $msg) {
                    $payload = json_decode((string) $msg, true);
                    if ($payload !== null) {
                        $this->handleMessage($payload);
                    }
                });

                $conn->on('close', function (int $code, string $reason) {
                    $this->debug("WebSocket closed: {$code} {$reason}");
                    if ($this->options->shouldReconnect()) {
                        $delay = $this->options->getReconnectDelay();
                        $this->loop->addTimer($delay, fn () => $this->connectWebSocket(self::WEBSOCKET_URL));
                    }
                });

                $conn->on('error', function (\Exception $e) {
                    $this->debug("WebSocket error: " . $e->getMessage());
                });
            },
            function (\Exception $e) {
                $this->debug("Failed to connect: " . $e->getMessage());
                if ($this->options->shouldReconnect()) {
                    $delay = $this->options->getReconnectDelay();
                    $this->loop->addTimer($delay, fn () => $this->connectWebSocket(self::WEBSOCKET_URL));
                }
            }
        );
    }

    private function handleMessage(array $payload): void
    {
        $this->debug(json_encode($payload));

        $messageType = $payload['metadata']['message_type'] ?? null;

        switch ($messageType) {
            case 'session_welcome':
                $sessionId = $payload['payload']['session']['id'];
                $this->subscribeToEvents($sessionId);
                break;

            case 'notification':
                $this->handleNotification($payload['payload']);
                break;

            case 'session_reconnect':
                $reconnectUrl = $payload['payload']['session']['reconnect_url'];
                $this->debug("Reconnecting to: {$reconnectUrl}");
                $this->connectWebSocket($reconnectUrl);
                break;

            case 'revocation':
                $type = $payload['payload']['subscription']['type'] ?? 'unknown';
                $status = $payload['payload']['subscription']['status'] ?? 'unknown';
                $this->debug("Subscription revoked — type: {$type}, status: {$status}");
                break;

            case 'session_keepalive':
                break;
        }
    }

    private function subscribeToEvents(string $sessionId): void
    {
        foreach ($this->options->getSubscriptions() as $subscription) {
            $body = json_encode([
                'type' => $subscription['type'],
                'version' => $subscription['version'] ?? '1',
                'condition' => $subscription['condition'],
                'transport' => [
                    'method' => 'websocket',
                    'session_id' => $sessionId,
                ],
            ]);

            $this->browser->post(self::API_SUBSCRIPTIONS_URL, [
                'Authorization' => 'Bearer ' . $this->options->getAccessToken(),
                'Client-Id' => $this->options->getClientId(),
                'Content-Type' => 'application/json',
            ], $body)->then(
                function (\Psr\Http\Message\ResponseInterface $response) use ($subscription) {
                    $this->debug("Subscribed to {$subscription['type']}: HTTP {$response->getStatusCode()}");
                },
                function (\Exception $e) use ($subscription) {
                    $this->debug("Failed to subscribe to {$subscription['type']}: " . $e->getMessage());
                }
            );
        }
    }

    private function handleNotification(array $payload): void
    {
        $subscriptionType = $payload['subscription']['type'] ?? null;
        $eventData = $payload['event'] ?? [];

        $event = match ($subscriptionType) {
            'channel.chat.message' => new ChatMessageEvent($eventData),
            'channel.chat.notification' => new ChatNotificationEvent($eventData),
            default => null,
        };

        if ($event !== null) {
            $this->eventHandler->invoke($event);

            if ($event instanceof ChatMessageEvent) {
                $this->parseCommand($event);
            }
        }
    }

    private function parseCommand(ChatMessageEvent $event): void
    {
        $text = trim($event->messageText);

        if ($text === '' || $text[0] !== '!') {
            return;
        }

        $parts = explode(' ', $text, 2);
        $name = strtolower($parts[0]);
        $args = isset($parts[1]) ? explode(' ', trim($parts[1])) : [];

        if (!isset($this->commands[$name])) {
            return;
        }

        ($this->commands[$name])(new CommandContext($event, $name, $args, $this));
    }

    private function debug(string $message): void
    {
        if ($this->options->isDebug()) {
            print $message . PHP_EOL;
        }
    }
}
