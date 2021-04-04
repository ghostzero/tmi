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
        $channel = Channel::sanitize($channel);
        $this->write("PRIVMSG {$channel} :{$message}");
    }

    public function whisper(string $user, string $message): void
    {
        $this->say('#tmi_inspector', sprintf('/w %s %s', $user, $message));
    }

    public function join(string $channel): void
    {
        $channel = Channel::sanitize($channel);
        $this->channels[$channel] = new Channel($channel, true);
        $this->write("JOIN {$channel}");
    }

    public function part(string $channel): void
    {
        $channel = Channel::sanitize($channel);
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
}
