<?php

namespace GhostZero\Tmi;

use Closure;
use GhostZero\Tmi\Events\EventHandler;
use GhostZero\Tmi\Messages\IrcMessage;
use GhostZero\Tmi\Messages\IrcMessageParser;
use React\Dns\Resolver\Factory;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use React\Promise\Promise;
use React\Socket\ConnectionInterface;
use React\Socket\DnsConnector;
use React\Socket\SecureConnector;
use React\Socket\TcpConnector;
use RuntimeException;

class Client
{
    use Traits\Irc;

    protected ConnectionInterface $connection;

    protected bool $connected;

    protected LoopInterface $loop;

    protected ClientOptions $options;

    protected IrcMessageParser $ircMessageParser;

    protected array $channels = [];

    protected EventHandler $eventHandler;

    protected CommandQueue $commandQueue;

    protected TimerInterface $timer;

    public function __construct(ClientOptions $options)
    {
        $this->options = $options;
        $this->loop = \React\EventLoop\Factory::create();
        $this->ircMessageParser = new IrcMessageParser();
        $this->eventHandler = new EventHandler();
        $this->commandQueue = new CommandQueue($options->getType());
    }

    public function connect(): void
    {
        $tcpConnector = new TcpConnector($this->loop);
        $dnsResolverFactory = new Factory();
        $dns = $dnsResolverFactory->createCached($this->options->getNameserver(), $this->loop);
        $dnsConnector = new DnsConnector($tcpConnector, $dns);
        $connectorPromise = $this->getConnectorPromise($dnsConnector);

        $connectorPromise->then(function (ConnectionInterface $connection) {
            $this->connection = $connection;
            $this->connected = true;
            $this->timer = $this->loop->addPeriodicTimer(
                $this->commandQueue->getInterval(),
                fn() => $this->commandQueue->call($connection)
            );

            // login & request all twitch Kappabilities
            $identity = $this->options->getIdentity();
            $this->write("PASS {$identity['password']}");
            $this->write("NICK {$identity['username']}");
            $this->write('CAP REQ :twitch.tv/membership twitch.tv/tags twitch.tv/commands');

            $channels = $this->options->getChannels();

            foreach ($channels as $channel) {
                $this->join($channel);
            }

            $this->connection->on('data', function ($data) {
                foreach ($this->ircMessageParser->parse($data) as $message) {
                    $this->handleIrcMessage($message);
                }
            });

            $this->connection->on('close', function () {
                $this->connected = false;
                $this->loop->cancelTimer($this->timer);
                $this->reconnect('Connection closed by Twitch.');
            });

            $this->connection->on('end', function () {
                $this->connected = false;
                $this->loop->cancelTimer($this->timer);
                $this->reconnect('Connection ended by Twitch.');
            });
        }, fn($error) => $this->reconnect($error));

        $this->loop->run();
    }

    public function close(): void
    {
        if ($this->isConnected()) {
            $this->connection->close();
            $this->loop->stop();
        }
    }

    public function write(string $rawCommand, string $queue = CommandQueue::QUEUE_DEFAULT): void
    {
        if (!$this->isConnected()) {
            throw new RuntimeException('No open connection was found to write commands to.');
        }

        // Make sure the command ends in a newline character
        if (mb_substr($rawCommand, -1) !== "\n") {
            $rawCommand .= "\n";
        }

        $this->commandQueue->push($rawCommand, $queue);
    }

    public function isConnected(): bool
    {
        return isset($this->connection) && $this->connected;
    }

    private function handleIrcMessage(IrcMessage $message): void
    {
        if ($this->options->isDebug()) {
            print $message->rawMessage . PHP_EOL;
        }

        $message->injectChannels($this->channels);
        $message->handle($this);

        foreach ($message->getEvents() as $event) {
            $this->eventHandler->invoke($event);
        }
    }

    public function getLoop(): LoopInterface
    {
        return $this->loop;
    }

    public function getOptions(): ClientOptions
    {
        return $this->options;
    }

    public function on(string $event, Closure $closure): self
    {
        $this->eventHandler->addHandler($event, $closure);

        return $this;
    }

    private function getConnectorPromise(DnsConnector $dnsConnector): Promise
    {
        if ($this->options->shouldConnectSecure()) {
            return (new SecureConnector($dnsConnector, $this->loop))
                ->connect(sprintf('%s:6697', $this->options->getServer()));
        }

        return $dnsConnector->connect(sprintf('%s:6667', $this->options->getServer()));
    }

    public function getCommandQueue(): CommandQueue
    {
        return $this->commandQueue;
    }

    private function reconnect($error): void
    {
        if ($this->options->shouldReconnect()) {
            if ($this->options->isDebug()) {
                print 'Initialize reconnect... Error: ' . $error . PHP_EOL;
            }
            $this->loop->addTimer(4.0, function () {
                $this->connect();
            });
        }
    }
}
