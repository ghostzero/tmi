<?php

namespace Tests\Feature;

use GhostZero\Tmi\Client;
use GhostZero\Tmi\ClientOptions;
use Tests\TestCase;

class OnOffCommandTest extends TestCase
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

        $called = false;

        $client->connect(function () use (&$called) {
            $called = true; // mark this test as successful
        });

        sleep(3);

        self::assertTrue($called, '');
    }
}
