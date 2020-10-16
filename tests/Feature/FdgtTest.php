<?php

namespace Tests\Feature;

use GhostZero\Tmi\Client;
use GhostZero\Tmi\ClientOptions;
use Tests\TestCase;

class FdgtTest extends TestCase
{
    /**
     * @medium
     */
    public function testExample(): void
    {
        $client = new Client(new ClientOptions([
            'options' => ['debug' => true],
            'connection' => [
                'secure' => false,
                'server' => 'irc.fdgt.dev',
            ],
            'channels' => ['ghostzero']
        ]));

        $client->on('registered', function () use ($client) {
            $this->assertTrue(true);
            $client->close();
        });

        $client->connect();
    }
}
