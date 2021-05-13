<?php

namespace Tests\Feature;

use GhostZero\Tmi\Client;
use GhostZero\Tmi\ClientOptions;
use GhostZero\Tmi\Events\Irc\NameReplyEvent;
use Tests\TestCase;

class ClientTest extends TestCase
{
    /**
     * @medium
     */
    public function testClientConnection(): void
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

        $client->on(NameReplyEvent::class, function (NameReplyEvent $event) {
            $this->assertEquals('#ghostzero', $event->channel);
            $event->client->close();
        });

        $client->connect();
    }
}
