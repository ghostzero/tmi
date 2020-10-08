<?php

namespace GhostZero\Tmi\Tests;

use GhostZero\Tmi\Client;
use GhostZero\Tmi\ClientOptions;
use PHPUnit\Framework\TestCase;

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

        $client->on('names', function (string $channel) use ($client) {
            $this->assertEquals('#ghostzero', $channel);
            $client->close();
        });

        $client->connect();
    }
}
