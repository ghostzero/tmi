<?php

namespace Tests\Feature;

use GhostZero\Tmi\Client;
use GhostZero\Tmi\ClientOptions;
use GhostZero\Tmi\Events\Irc\WelcomeEvent;
use Tests\TestCase;

class FdgtTest extends TestCase
{
    /**
     * @medium
     */
    public function testFdgtConnection(): void
    {
        self::markTestSkipped('Skipped. See https://github.com/fdgt-apis/api/issues/125');

        $client = new Client(new ClientOptions([
            'options' => ['debug' => true],
            'connection' => [
                'secure' => true,
                'server' => 'irc.fdgt.dev',
            ],
            'channels' => ['ghostzero']
        ]));

        $client->on(WelcomeEvent::class, function () use ($client) {
            $this->assertTrue(true);
            $client->close();
        });

        $client->connect();
    }
}
