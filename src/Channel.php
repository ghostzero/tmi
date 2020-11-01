<?php

namespace GhostZero\Tmi;

class Channel
{
    private string $channel;

    private string $topic = '';

    private array $users = [];

    public function __construct(string $channel)
    {
        $this->channel = $channel;
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

    public function __toString()
    {
        return $this->getName();
    }
}
