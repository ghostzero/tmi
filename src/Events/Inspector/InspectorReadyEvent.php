<?php

namespace GhostZero\Tmi\Events\Inspector;

use GhostZero\Tmi\Events\Event;

class InspectorReadyEvent extends Event
{
    public string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }
}
