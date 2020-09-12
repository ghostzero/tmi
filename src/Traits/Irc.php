<?php

namespace GhostZero\Tmi\Traits;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Client;

/**
 * @mixin Client
 */
trait Irc
{
    public function say(string $channel, string $message): void
    {
        $channel = $this->channelName($channel);
        $this->write("PRIVMSG {$channel} :{$message}");
    }

    public function join(string $channel): void
    {
        $channel = $this->channelName($channel);
        $this->channels[$channel] = new Channel($channel);
        $this->write("JOIN {$channel}");
    }

    public function part(string $channel): void
    {
        $channel = $this->channelName($channel);
        unset($this->channels[$channel]);
        $this->write("PART {$channel}");
    }

    public function getChannels(): array
    {
        return $this->channels;
    }

    public function getChannel(string $channel): Channel
    {
        return $this->channels[$channel];
    }

    private function channelName(string $channel): string
    {
        if ($channel[0] !== '#') {
            $channel = "#$channel";
        }

        return $channel;
    }
}
