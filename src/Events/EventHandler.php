<?php

namespace GhostZero\Tmi\Events;

class EventHandler
{
    private array $eventHandlers;

    public function __construct()
    {
        $this->eventHandlers = [];
    }

    public function addHandler(string $event, callable $function): void
    {
        if (!array_key_exists($event, $this->eventHandlers)) {
            $this->eventHandlers[$event] = [];
        }

        $this->eventHandlers[$event][] = $function;
    }

    public function invoke(Event $event): void
    {
        $handlers = $this->eventHandlers['*'] ?? [];
        $this->invokeHandlers($handlers, $event);
        $handlers = $this->eventHandlers[get_class($event)] ?? [];
        $this->invokeHandlers($handlers, $event);
    }

    protected function invokeHandlers(array $handlers, Event $event): void
    {
        foreach ($handlers as $handler) {
            $handler($event);
        }
    }
}
