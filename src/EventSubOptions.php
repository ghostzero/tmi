<?php

namespace GhostZero\Tmi;

class EventSubOptions
{
    private array $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function getAccessToken(): string
    {
        return ltrim($this->options['access_token'], 'oauth:');
    }

    public function getClientId(): string
    {
        return $this->options['client_id'];
    }

    /**
     * Returns the list of EventSub subscriptions to register.
     *
     * Each entry must have:
     *   - type:      e.g. 'channel.chat.message'
     *   - version:   (optional, defaults to '1')
     *   - condition: associative array of condition fields
     *
     * Example:
     *   ['type' => 'channel.chat.message', 'condition' => ['broadcaster_user_id' => '123', 'user_id' => '456']]
     */
    public function getSubscriptions(): array
    {
        return $this->options['subscriptions'] ?? [];
    }

    /**
     * Returns the bot's Twitch user ID used as the sender when posting chat messages.
     * Reads from the top-level 'user_id' option, falling back to the first subscription's condition.
     */
    public function getBotUserId(): string
    {
        if (isset($this->options['user_id'])) {
            return $this->options['user_id'];
        }

        foreach ($this->options['subscriptions'] ?? [] as $sub) {
            if (isset($sub['condition']['user_id'])) {
                return $sub['condition']['user_id'];
            }
        }

        throw new \RuntimeException('Bot user ID not configured. Add "user_id" to EventSubOptions or include it in a subscription condition.');
    }

    public function isDebug(): bool
    {
        return $this->options['options']['debug'] ?? false;
    }

    public function shouldReconnect(): bool
    {
        return $this->options['connection']['reconnect'] ?? true;
    }

    public function getReconnectDelay(): int
    {
        return $this->options['connection']['reconnect_delay'] ?? 3;
    }
}
