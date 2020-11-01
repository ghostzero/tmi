<?php

namespace GhostZero\Tmi\Events\Inspector;

use GhostZero\Tmi\Events\Event;
use stdClass;

class InspectorEvent extends Event
{
    public stdClass $payload;

    public function __construct(stdClass $payload)
    {
        $this->payload = $payload;
    }
}
