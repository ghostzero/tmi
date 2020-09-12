<?php

namespace GhostZero\Tmi\Events;

class Event
{
    private string $event;

    private array $arguments;

    public function __construct(string $event, array $arguments = [])
    {
        $this->event = $event;
        $this->arguments = $arguments;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getEvent(): string
    {
        return $this->event;
    }
}
