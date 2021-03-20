<?php

namespace GhostZero\Tmi\Events\Inspector;

use GhostZero\Tmi\Events\Event;

/**
 * Triggers when an inspector.tmi.dev session was recognized.
 */
class InspectorReadyEvent extends Event
{
    /**
     * @var string URL of the Inspector session.
     */
    public string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }
}
