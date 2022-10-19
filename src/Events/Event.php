<?php

namespace GhostZero\Tmi\Events;

use GhostZero\Tmi\Client;
use ReflectionClass;
use ReflectionException;
use Serializable;

class Event implements Serializable
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
     *
     * @inheritdoc
     */
    public function serialize()
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
        return serialize($data);
    }

    /**
     * @inheritdoc
     * @throws ReflectionException
     */
    public function unserialize($data)
    {
        $props = unserialize($data);
        $reflect = new ReflectionClass($this);
        foreach ($props as $name => $prop) {
            if ($prop['s']) {
                $reflect->getProperty($name)->setValue($prop['v']);
            } else {
                $reflect->getProperty($name)->setValue($this, $prop['v']);
            }
        }
    }
}
