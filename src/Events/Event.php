<?php

namespace GhostZero\Tmi\Events;

use GhostZero\Tmi\Client;
use ReflectionClass;

class Event
{
    /**
     * @var Client|null Client object. Can be null if serialized.
     */
    public ?Client $client = null;

    public function signature(): ?string
    {
        return null;
    }

    /**
     * Override serializer, since we cannot serialize the client object.
     */
    public function __serialize(): array
    {
        $data = [];
        $reflect = new ReflectionClass($this);
        $props = $reflect->getProperties();

        foreach ($props as $prop) {
            if ($prop->getName() === 'client') {
                continue;
            }
            $data[$prop->getName()] = ['s' => $prop->isStatic(), 'v' => $prop->getValue($this)];
        }

        return $data;
    }

    public function __unserialize(array $data): void
    {
        $reflect = new ReflectionClass($this);

        foreach ($data as $name => $prop) {
            if ($prop['s']) {
                $reflect->getProperty($name)->setValue($prop['v']);
            } else {
                $reflect->getProperty($name)->setValue($this, $prop['v']);
            }
        }
    }
}
