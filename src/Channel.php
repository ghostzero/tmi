<?php

namespace GhostZero\Tmi;

class Channel
{
    private string $channel;

    private string $topic = '';

    private array $users = [];

    private bool $persisted;

    public function __construct(string $channel, bool $persisted = false)
    {
        $this->channel = $channel;
        $this->persisted = $persisted;
    }

    public function getName(): string
    {
        return $this->channel;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): void
    {
        $this->topic = $topic;
    }

    public function getUsers(): array
    {
        return $this->users;
    }

    public function setUsers(array $users): void
    {
        $this->users = $users;
    }

    public function isPersisted(): bool
    {
        return $this->persisted;
    }

    public function __toString()
    {
        return $this->getName();
    }

    
    public function formatName(): string
    {
        return str_replace('#','',$this->getName());
    }
    
    public static function sanitize(string $channel): string
    {
        if ($channel[0] !== '#') {
            $channel = "#$channel";
        }

        return strtolower($channel);
    }
}
