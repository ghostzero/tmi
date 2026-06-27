<?php

namespace GhostZero\Tmi;

use GhostZero\Tmi\Events\EventSub\ChatMessageEvent;

class CommandContext
{
    public ChatMessageEvent $event;
    public string $command;

    /**
     * Arguments after the command name, split by spaces.
     * @var string[]
     */
    public array $args;

    private EventSubClient $client;

    public function __construct(ChatMessageEvent $event, string $command, array $args, EventSubClient $client)
    {
        $this->event = $event;
        $this->command = $command;
        $this->args = $args;
        $this->client = $client;
    }

    /**
     * Sends a Twitch reply thread to the triggering message.
     */
    public function reply(string $message): void
    {
        $this->client->sendMessage($this->event->broadcasterUserId, $message, $this->event->messageId);
    }

    /**
     * Sends a regular chat message (no reply thread).
     */
    public function say(string $message): void
    {
        $this->client->sendMessage($this->event->broadcasterUserId, $message);
    }
}
