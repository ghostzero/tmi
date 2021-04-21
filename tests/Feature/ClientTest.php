<?php

namespace Tests\Feature;

use GhostZero\Tmi\Client;
use GhostZero\Tmi\ClientOptions;
use GhostZero\Tmi\Events\Irc\NameReplyEvent;
use Tests\TestCase;

class ClientTest extends TestCase
{
    public function testExample(): void
    {
        $client = new Client(new ClientOptions([
            'options' => ['debug' => true],
            'connection' => [
                'secure' => true,
                'reconnect' => true,
                'rejoin' => true,
            ],
            'channels' => ['ghostzero']
        ]));

        $client->on(NameReplyEvent::class, function (NameReplyEvent $event) use ($client) {
            $this->assertEquals('#ghostzero', $event->channel);
            $client->close();
        });

        $client->connect();
    }
}
