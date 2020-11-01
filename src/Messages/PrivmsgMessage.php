<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Events\Irc\PrivmsgEvent;

class PrivmsgMessage extends IrcMessage
{
    public Channel $channel;

    public string $message;

    public string $target;

    public string $user;

    public function __construct(string $message)
    {
        parent::__construct($message);
        $this->user = strstr($this->source, '!', true);
        $this->target = $this->commandSuffix;
        $this->message = $this->payload;
    }

    public function handle(Client $client, array $channels): array
    {
        if (array_key_exists($this->target, $channels)) {
            $this->channel = $channels[$this->target];
        }

        $self = $client->getOptions()->getNickname() === $this->user;

        if ($this->user === 'tmi_inspector') {
            $payload = json_decode($this->message, false, 512, JSON_THROW_ON_ERROR);
            $events = [
                new Event('inspector_payload', [$payload]),
            ];
            if ($payload->event === 'ready') {
                $events[] = new Event('inspector', [$payload->url]);
            }

            return $events;
        }

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
                new Event('message', [$this->channel, $this->tags, $this->user, $this->message, $self])
            ];

            if ($this->tags['bits']) {
                $events[] = new Event('cheer', [$this->channel, $this->tags, $this->user, $this->message, $self]);
            }

            return $events;
        }

        return [
            new Event('privmsg', [$this->target, $this->tags, $this->user, $this->message, $self]),
            new Event(PrivmsgEvent::class, [new PrivmsgEvent($this->target, $this->tags, $this->user, $this->message, $self)]),
        ];
    }
}
