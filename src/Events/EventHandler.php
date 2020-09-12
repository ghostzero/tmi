<?php

namespace GhostZero\Tmi\Events;

class EventHandler
{
    private array $eventHandlers;

    public function __construct()
    {
        $this->eventHandlers = [];
    }

    public function addHandler($event, ?callable $function): void
    {
        if (is_callable($event)) {
            $function = $event;
            $event = '*';
        }

        if (!array_key_exists($event, $this->eventHandlers)) {
            $this->eventHandlers[$event] = [];
        }

        $this->eventHandlers[$event][] = $function;
    }

    public function invoke(Event $event): void
    {
        $handlers = array_merge($this->eventHandlers['*'] ?? [], $this->eventHandlers[$event->getEvent()] ?? []);
        foreach ($handlers as $handler) {
            $handler(...$event->getArguments());
        }
    }
}
