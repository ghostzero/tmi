<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Events\Twitch;
use GhostZero\Tmi\Plan;

class UserNoticeMessage extends IrcMessage
{
    public const TAG_ANONGIFTPAIDUPGRADE = 'anongiftpaidupgrade';
    public const TAG_ANONSUBGIFT = 'anonsubgift';
    public const TAG_ANONSUBMYSTERYGIFT = 'anonsubmysterygift';
    public const TAG_GIFTPAIDUPGRADE = 'giftpaidupgrade';
    public const TAG_PRIMEPAIDUPGRADE = 'primepaidupgrade';
    public const TAG_RAID = 'raid';
    public const TAG_RESUB = 'resub';
    public const TAG_RITUAL = 'ritual';
    public const TAG_SUB = 'sub';
    public const TAG_SUBGIFT = 'subgift';
    public const TAG_SUBMYSTERYGIFT = 'submysterygift';

    public Channel $channel;

    public string $message;

    public function __construct(string $message)
    {
        parent::__construct($message);
        $this->message = $message;
    }

    public function handle(Client $client, array $channels): array
    {
        if (array_key_exists($this->commandSuffix, $channels)) {
            $this->channel = $channels[$this->commandSuffix];
        } else {
            $this->channel = new Channel($this->commandSuffix);
        }

        $msgId = $this->tags['msg-id'] ?? '';

        $events = [
            new Twitch\UserNoticeEvent($this->channel, $msgId)
        ];

        if ($event = $this->getArguments($msgId)) {
            $events[] = $event;
        }

        return $events;
    }

    public function getArguments(string $msgId): ?Event
    {
        $tags = $this->tags;
        $username = $tags['display-name'] ?? $tags['login'] ?? '';
        $subPlan = $tags['msg-param-sub-plan'] ?? '';
        $planName = $tags['msg-param-sub-plan-name'] ?? '';
        $prime = strpos($subPlan, 'Prime') !== false;
        $plan = new Plan($prime, $subPlan, $planName);
        $streakMonths = (int)($tags['msg-param-streak-months'] ?? 0);
        $recipient = $tags['msg-param-recipient-display-name'] ?? $tags['msg-param-recipient-user-name'] ?? '';
        $giftSubCount = (int)($tags['msg-param-mass-gift-count'] ?? 0);
        $sender = $tags['msg-param-sender-name'] ?? $tags['msg-param-sender-login'] ?? '';
        $raidedChannel = $tags['msg-param-displayName'] ?? $tags['msg-param-login'] ?? '';
        $viewers = (int)($tags['msg-param-viewerCount'] ?? 0);
        $ritual = $tags['msg-param-ritual-name'] ?? '';
        $message = $this->payload ?? '';

        switch ($msgId) {
            case self::TAG_ANONGIFTPAIDUPGRADE:
                return new Twitch\AnonGiftPaidUpgradeEvent($this->channel, $username, $tags);
            case self::TAG_ANONSUBGIFT:
                return new Twitch\AnonSubGiftEvent($this->channel, $streakMonths, $recipient, $plan, $tags);
            case self::TAG_ANONSUBMYSTERYGIFT:
                return new Twitch\AnonSubMysteryGiftEvent($this->channel, $giftSubCount, $plan, $tags);
            case self::TAG_GIFTPAIDUPGRADE:
                return new Twitch\GiftPaidUpgradeEvent($this->channel, $username, $sender, $tags);
            case self::TAG_PRIMEPAIDUPGRADE:
                return new Twitch\PrimePaidUpgradeEvent($this->channel, $username, $plan, $tags);
            case self::TAG_RAID:
                return new Twitch\RaidEvent($this->channel, $raidedChannel, $viewers);
            case self::TAG_RESUB:
                return new Twitch\ResubEvent($this->channel, $username, $streakMonths, $message, $tags, $plan);
            case self::TAG_RITUAL:
                return new Twitch\RitualEvent($this->channel, $ritual, $username, $tags, $message);
            case self::TAG_SUB:
                return new Twitch\SubEvent($this->channel, $username, $plan, $message, $tags);
            case self::TAG_SUBGIFT:
                return new Twitch\SubGiftEvent($this->channel, $username, $streakMonths, $recipient, $plan, $tags);
            case self::TAG_SUBMYSTERYGIFT:
                return new Twitch\SubMysteryGiftEvent($this->channel, $username, $giftSubCount, $plan, $tags);
            default:
                return null;
        }
    }
}
