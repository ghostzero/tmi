<?php

namespace GhostZero\Tmi\Events\Irc;

use GhostZero\Tmi\Events\Event;

/**
 * About once every five minutes, the IRC server will send a ping.
 *
 * To ensure that your connection to the server is not prematurely
 * terminated, the TMI.php client will automatically reply with
 * a ping. No further actions are required.
 */
class PingEvent extends Event
{

}
