<?php

namespace GhostZero\Tmi\Events\Inspector;

use GhostZero\Tmi\Events\Event;
use stdClass;

/**
 * Triggered when the inspector receives data from the server.
 */
class InspectorEvent extends Event
{
    /**
     * @var stdClass    Contains the session.
     */
    public stdClass $payload;

    public function __construct(stdClass $payload)
    {
        $this->payload = $payload;
    }
}
