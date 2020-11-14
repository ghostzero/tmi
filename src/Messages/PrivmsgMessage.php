<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Inspector\InspectorEvent;
use GhostZero\Tmi\Events\Inspector\InspectorReadyEvent;
use GhostZero\Tmi\Events\Irc\PrivmsgEvent;
use GhostZero\Tmi\Events\Twitch\CheerEvent;
use GhostZero\Tmi\Events\Twitch\HostedEvent;
use GhostZero\Tmi\Events\Twitch\MessageEvent;

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
        } else {
            $this->channel = new Channel($this->target);
        }

        $self = $client->getOptions()->getNickname() === $this->user;

        if ($this->user === 'tmi_inspector') {
            $payload = json_decode($this->message, false, 512, JSON_THROW_ON_ERROR);
            $events = [new InspectorEvent($payload)];
            if ($payload->event === 'ready') {
                $events[] = new InspectorReadyEvent($payload->url);
            }

            return $events;
        }

        if ($this->user === 'jtv') {
            $autohost = (bool)strpos($this->message, 'auto');
            if ((bool)strpos($this->message, 'hosting you for')) {
                $count = (int)explode(' ', substr($this->message, strpos($this->message, 'hosting you for')))[3];
                return [
                    new HostedEvent($this->channel, $this->user, $count, $autohost)
                ];
            }

            if ((bool)strpos($this->message, 'hosting you')) {
                return [
                    new HostedEvent($this->channel, $this->user, 0, $autohost),
                ];
            }
        } elseif ($this->target[0] === '#') {
            $events = [
                new MessageEvent($this->channel, $this->tags, $this->user, $this->message, $self)
            ];

            if ($this->tags['bits']) {
                $events[] = new CheerEvent($this->channel, $this->tags, $this->user, $this->message, $self);
            }

            return $events;
        }

        return [
            new PrivmsgEvent($this->target, $this->tags, $this->user, $this->message, $self)
        ];
    }
}
