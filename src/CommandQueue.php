<?php

namespace GhostZero\Tmi;

use React\Socket\ConnectionInterface;

/**
 * Authentication and join rate limits are:
 * - 20 authenticate attempts per 10 seconds per user (200 for verified bots)
 * - 20 join attempts per 10 seconds per user (2000 for verified bots)
 *
 * Command and message limits are:
 * - 20 per 30 seconds - Users sending commands or messages to channels in
 *      which they do not have Moderator or Operator status.
 * - 100 per 30 seconds - Users sending commands or messages to channels in
 *      which they have Moderator or Operator status.
 *
 * For Whispers, which are private chat message between two users:
 * - 3 per second, up to 100 per minute 40 accounts per day - Users (not bots)
 * - 10 per second, up to 200 per minute 500 accounts per day - Known bots
 * - 20 per second, up to 1200 per minute 100,000 accounts per day - Verified bots
 *
 * @see https://dev.twitch.tv/docs/irc/guide#known-and-verified-bots
 */
class CommandQueue
{
    public const QUEUE_JOIN = 'join';
    public const QUEUE_DEFAULT = 'default';

    private float $interval;
    private array $messageQueue = [];

    public function __construct(string $type)
    {
        $this->interval = 0.01; // 100 messages per second
        $this->messageQueue[self::QUEUE_DEFAULT] = [];
        $this->messageQueue[self::QUEUE_JOIN] = [];
    }

    public function getInterval(): float
    {
        return $this->interval;
    }

    public function push(string $rawCommand, string $queue): void
    {
        $this->messageQueue[$queue][] = $rawCommand;
    }

    public function getSize(): int
    {
        return array_sum([
            count($this->messageQueue[self::QUEUE_JOIN]),
            count($this->messageQueue[self::QUEUE_DEFAULT]),
        ]);
    }

    public function call(ConnectionInterface $connection): void
    {
        $messages = [
            array_shift($this->messageQueue[self::QUEUE_DEFAULT]),
            array_shift($this->messageQueue[self::QUEUE_JOIN]),
        ];

        foreach ($messages as $message) {
            $connection->write($message);
        }
    }
}
