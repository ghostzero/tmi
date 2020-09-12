<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Client;
use GhostZero\Tmi\Tags;

class IrcMessage
{
    public string $rawMessage;

    protected string $command;

    protected string $commandSuffix;

    protected bool $handled;

    protected string $payload;

    protected string $source;

    protected Tags $tags;

    public function __construct(string $command)
    {
        $this->handled = false;
        $this->parse($command);
    }

    public function handle(Client $client, bool $force = false): void
    {
        if ($this->handled && !$force) {
            return;
        }
    }

    public function getEvents(): array
    {
        return [];
    }

    public function injectChannel(array $channels): void
    {
        //
    }

    private function parse(string $command): void
    {
        $command = trim($command);
        $this->rawMessage = $command;
        $i = 0;

        if ($command[0] === ':') {
            $i = strpos($command, ' ');
            $this->source = substr($command, 1, $i - 1);

            $i++;
        }

        $j = strpos($command, ' ', $i);
        if ($j !== false) {
            $this->command = substr($command, $i, $j - $i);
        } else {
            $this->command = substr($command, $i);

            return;
        }

        $i = strpos($command, ':', $j);
        if ($i !== false) {
            if ($i !== $j + 1) {
                $this->commandSuffix = substr($command, $j + 1, $i - $j - 2);
            }
            $this->payload = substr($command, $i + 1);
        } else {
            $this->commandSuffix = substr($command, $j + 1);
        }
    }

    public function withTags(Tags $tags): self
    {
        $this->tags = $tags;
        return $this;
    }
}
