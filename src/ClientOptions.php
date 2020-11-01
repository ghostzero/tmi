<?php

namespace GhostZero\Tmi;

class ClientOptions
{
    private array $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function isDebug(): bool
    {
        return $this->options['options']['debug'] ?? false;
    }

    public function getIdentity(): array
    {
        $default = ['username' => 'justinfan' . random_int(1000, 80000), 'password' => 'SCHMOOPIIE'];
        return $this->options['identity'] ?? $default;
    }

    public function getChannels(): array
    {
        return $this->options['channels'] ?? [];
    }

    public function getNickname(): string
    {
        return $this->options['identity']['username'] ?? 'justinfan1337';
    }

    public function getServer(): string
    {
        return $this->options['connection']['server'] ?? 'irc.chat.twitch.tv';
    }

    public function shouldAutoRejoin(): bool
    {
        return $this->options['connection']['rejoin'] ?? true;
    }

    public function shouldReconnect(): bool
    {
        return $this->options['connection']['reconnect'] ?? true;
    }

    public function shouldConnectSecure(): bool
    {
        return $this->options['connection']['secure'] ?? true;
    }

    public function getNameserver(): string
    {
        return $this->options['connection']['nameserver'] ?? '1.1.1.1';
    }

    public function getType(): string
    {
        return $this->options['identity']['type'] ?? 'verified';
    }
}
