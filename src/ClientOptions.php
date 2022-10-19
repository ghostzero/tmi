<?php

namespace GhostZero\Tmi;

class ClientOptions
{
    private array $options;

    public function __construct(array $options)
    {
        $this->options = $options;

        if (empty($this->options['identity']['username']) || empty($this->options['identity']['password'])) {
            $this->options['identity'] = [
                'username' => $this->options['identity']['username'] ?? ('justinfan' . random_int(1000, 80000)),
                'password' => 'SCHMOOPIIE'
            ];
        }
    }

    public function isDebug(): bool
    {
        return $this->options['options']['debug'] ?? false;
    }

    public function getExecutionTimeout(): float
    {
        return (float)($this->options['options']['execution_timeout'] ?? 1.5);
    }

    public function getIdentity(): array
    {
        return $this->options['identity'];
    }

    public function getChannels(): array
    {
        return $this->options['channels'] ?? [];
    }

    public function getNickname(): string
    {
        return $this->options['identity']['username'];
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

    public function setShouldReconnect(bool $reconnect): void
    {
        $this->options['connection']['reconnect'] = $reconnect;
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

    public function getReconnectDelay(): int
    {
        return $this->options['connection']['reconnect_delay'] ?? 3;
    }
}
