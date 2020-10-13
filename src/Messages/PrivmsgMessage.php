<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Event;

class PrivmsgMessage extends IrcMessage
{
    public Channel $channel;

    public string $message;

    public string $target;

    public string $user;
    private bool $self = false;

    public function __construct(string $message)
    {
        parent::__construct($message);
        $this->user = strstr($this->source, '!', true);
        $this->target = $this->commandSuffix;
        $this->message = $this->payload;
    }

    public function handle(Client $client, bool $force = false): void
    {
        if ($this->handled && !$force) {
            return;
        }

        $this->self = $client->getOptions()->getNickname() === $this->user;
    }

    public function getEvents(): array
    {
        if ($this->user === 'jtv') {
            $autohost = (bool)strpos($this->message, 'auto');
            if ((bool)strpos($this->message, 'hosting you for')) {
                $count = (int)explode(' ', substr($this->message, strpos($this->message, 'hosting you for')))[3];
                return [new Event('hosted', [$this->channel, $this->user, $count, $autohost])];
            }

            if ((bool)strpos($this->message, 'hosting you')) {
                return [new Event('hosted', [$this->channel, $this->user, 0, $autohost])];
            }
        } elseif ($this->target[0] === '#') {
            $events = [
                new Event('message', [$this->channel, $this->tags, $this->user, $this->message, $this->self])
            ];

            if ($this->tags['bits']) {
                $events[] = new Event('cheer', [$this->channel, $this->tags, $this->user, $this->message, $this->self]);
            }

            return $events;
        }

        return [new Event('privmsg', [$this->user, $this->tags, $this->target, $this->message, $this->self])];
    }

    public function injectChannels(array $channels): void
    {
        if (array_key_exists($this->target, $channels)) {
            $this->channel = $channels[$this->target];
        }
    }
}
