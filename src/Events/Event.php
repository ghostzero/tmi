<?php

namespace GhostZero\Tmi\Events;

use GhostZero\Tmi\Client;

class Event
{
    /**
     * @var Client Client object
     */
    public Client $client;

    public function signature(): ?string
    {
        return null;
    }
}
