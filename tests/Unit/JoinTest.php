<?php

namespace Tests\Unit;

use GhostZero\Tmi\Exceptions\ParseException;
use GhostZero\Tmi\Messages\IrcMessageParser;
use Tests\TestCase;
use Throwable;

class JoinTest extends TestCase
{
    public function testJoinMessage(): void
    {
        try {
            $ircMessageParser = new IrcMessageParser();
            foreach ($ircMessageParser->parse('JOIN #test') as $message) {
                self::assertNotNull($message);
            }
        } catch (Throwable $throwable) {
            self::assertInstanceOf(ParseException::class, $throwable);
        }
    }
}