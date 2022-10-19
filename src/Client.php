<?php

namespace GhostZero\Tmi;

use co;
use GhostZero\Tmi\Events\EventHandler;
use GhostZero\Tmi\Events\WithinCoroutine;
use GhostZero\Tmi\Exceptions\ParseException;
use GhostZero\Tmi\Messages\IrcMessage;
use GhostZero\Tmi\Messages\IrcMessageParser;
use RuntimeException;
use Swoole\Client as SwooleClient;
use Swoole\Coroutine;
use Swoole\Runtime;
use function Co\run;

class Client
{
    use Traits\Irc;

    protected bool $connected;

    protected ClientOptions $options;

    protected IrcMessageParser $ircMessageParser;

    protected array $channels = [];

    protected EventHandler $eventHandler;

    private SwooleClient $connection;

    public function __construct(ClientOptions $options)
    {
        Runtime::enableCoroutine(SWOOLE_HOOK_ALL);

        $this->options = $options;
        $this->ircMessageParser = new IrcMessageParser();
        $this->eventHandler = new EventHandler();
    }

    public function connect(?callable $execute = null): void
    {
        $this->connection = new SwooleClient(SWOOLE_SOCK_TCP);

        // Connect to the remote server, handle any errors as well...
        if (!$this->connection->connect('irc.chat.twitch.tv', 6667, 10)) {
            echo "Connection Failed. Error: {$this->connection->errCode}\n";
        }

        $this->connected = true;

        // login & request all twitch Kappabilities
        $identity = $this->options->getIdentity();
        $this->write("PASS {$identity['password']}");
        $this->write("NICK {$identity['username']}");
        $this->write('CAP REQ :twitch.tv/membership twitch.tv/tags twitch.tv/commands');

        if (!is_null($execute)) {
            //
            // $this->loop->addTimer($this->options->getExecutionTimeout(), fn() => $this->close());

            $execute();
        }


        while (true) {
            // Keep reading data in using this loop
            $data = $this->connection->recv();

            // Check if we have any data or not
            if (strlen($data) > 0) {
                print_r('connection receive data');
                foreach (explode("\n", $data) as $message) {
                    if (empty(trim($message))) {
                        continue;
                    }

                    try {
                        $this->handleIrcMessage($this->ircMessageParser->parseSingle($message));
                    } catch (ParseException $exception) {
                        $this->debug($exception->getMessage());
                    }
                }
            } else {
                // An empty string means the connection has been closed
                if ($data === '') {
                    // We must close the connection to use the client again
                    $this->connection->close();
                    $this->connected = false;

                    if (is_null($execute)) {
                        $this->reconnect('Connection closed by Twitch.');
                    }
                    break;
                } else {
                    // False means there was an error we need to check
                    if ($data === false) {
                        // You should use $client->errCode to handle errors yourself

                        // A timeout error will not close the connection.
                        if ($this->connection->errCode !== SOCKET_ETIMEDOUT) {
                            // Not a timeout, close the connection due to an error
                            $this->connection->close();
                            $this->connected = false;

                            if (is_null($execute)) {
                                $this->reconnect('Connection closed by Twitch.');
                            }
                            break;
                        }
                    } else {
                        // Unknown error, close and break out of the loop
                        $this->connection->close();
                        $this->connected = false;

                        if (is_null($execute)) {
                            $this->reconnect('Connection closed by Twitch.');
                        }
                        break;
                    }
                }
            }

            // Wait 1 second before reading data again on our loop
            Co::sleep(1);
        }

        while (true) {
            sleep(1);
        }
    }

    public function close(): void
    {
        $this->options->setShouldReconnect(false);

        if ($this->isConnected()) {
            $this->connection->close();
        }
    }

    public function write(string $rawCommand): void
    {
        if (!$this->isConnected()) {
            throw new RuntimeException('No open connection was found to write commands to.');
        }

        // Make sure the command ends in a newline character
        if (mb_substr($rawCommand, -1) !== "\n") {
            $rawCommand .= "\n";
        }

        $this->connection->send($rawCommand);
    }

    public function isConnected(): bool
    {
        return isset($this->connection) && $this->connected;
    }

    private function handleIrcMessage(IrcMessage $message): void
    {
        $this->debug($message->rawMessage);

        $events = $message->handle($this, $this->channels);

        foreach ($events as $event) {
            $event->client = $this; // attach client to event
            print_r(get_class($event));
            Coroutine::create(function () use ($event) {
                $this->eventHandler->invoke($event);
            });
        }
    }

    public function getOptions(): ClientOptions
    {
        return $this->options;
    }

    public function getEventHandler(): EventHandler
    {
        return $this->eventHandler;
    }

    public function any(callable $closure): self
    {
        return $this->on('*', $closure);
    }

    public function on(string $event, callable $closure): self
    {
        $this->eventHandler->addHandler($event, $closure);

        return $this;
    }

    private function getConnectionPort(): int
    {
        if ($this->options->shouldConnectSecure()) {
            return 6697;
        }

        return 6667;
    }

    private function reconnect($error): void
    {
        if ($this->options->shouldReconnect()) {
            $seconds = $this->options->getReconnectDelay();
            $this->debug("Initialize reconnect in {$seconds} seconds... Error: " . $error);
            sleep($seconds);
            $this->connect();
        }
    }

    private function debug(string $message): void
    {
        if ($this->options->isDebug()) {
            print $message . PHP_EOL;
        }
    }
}
