<?php

namespace GhostZero\Tmi\Messages;

use DomainException;
use GhostZero\Tmi\Events\Event;

class UserNoticeMessage extends IrcMessage
{
    public const TAG_ANONGIFTPAIDUPGRADE = 'anongiftpaidupgrade';
    public const TAG_ANONSUBGIFT = 'anonsubgift';
    public const TAG_ANONSUBMYSTERYGIFT = 'anonsubmysterygift';
    public const TAG_GIFTPAIDUPGRADE = 'giftpaidupgrade';
    public const TAG_PRIMEPAIDUPGRADE = 'primepaidupgrade';
    public const TAG_RAID = 'raid';
    public const TAG_RESUB = 'resub';
    public const TAG_RITUAl = 'ritual';
    public const TAG_SUB = 'sub';
    public const TAG_SUBGIFT = 'subgift';
    public const TAG_SUBMYSTERYGIFT = 'submysterygift';

    public string $channel;

    public string $message;

    public function __construct(string $message)
    {
        parent::__construct($message);

        $this->channel = strstr($this->commandSuffix, '#');
        $this->message = $message;
    }

    public function getEvents(): array
    {
        $msgId = $this->tags['msg-id'] ?? '';
        $arguments = $this->getArguments($msgId);
        $newChatter = (($msgId === self::TAG_RITUAl) && ($arguments[0] === 'new_chatter'));

        return [
            new Event('usernotice', [$this->channel, $msgId]),
            new Event($newChatter ? 'newchatter' : $this->getEventName($msgId), $arguments),
        ];
    }

    public function getEventName($msgId): string
    {
        switch ($msgId) {
            case self::TAG_SUB:
                return 'subscription';
            case self::TAG_RAID:
                return 'raided';
            default:
                return $msgId;
        }
    }

    public function getArguments(string $msgId): array
    {
        $tags = $this->tags;
        $username = $tags['display-name'] ?? $tags['login'] ?? '';
        $plan = $tags['msg-param-sub-plan'] ?? '';
        $planName = $tags['msg-param-sub-plan-name'] ?? '';
        $prime = strpos($plan, 'Prime') !== false;
        $methods = ['prime' => $prime, 'plan' => $plan, 'planName' => $planName];
        $userState = $tags;
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
                return [$this->channel, $username, $userState];
            case self::TAG_ANONSUBGIFT:
                return [$this->channel, $streakMonths, $recipient, $methods, $userState];
            case self::TAG_ANONSUBMYSTERYGIFT:
                return [$this->channel, $giftSubCount, $methods, $userState];
            case self::TAG_GIFTPAIDUPGRADE:
                return [$this->channel, $username, $sender, $userState];
            case self::TAG_PRIMEPAIDUPGRADE:
                return [$this->channel, $username, $methods, $userState];
            case self::TAG_RAID:
                return [$this->channel, $raidedChannel, $viewers];
            case self::TAG_RESUB:
                return [$this->channel, $username, $streakMonths, $message, $userState, $methods];
            case self::TAG_RITUAl:
                return [$this->channel, $ritual, $username];
            case self::TAG_SUB:
                return [$this->channel, $username, $methods, $message, $userState];
            case self::TAG_SUBGIFT:
                return [$this->channel, $username, $streakMonths, $recipient, $methods, $userState];
            case self::TAG_SUBMYSTERYGIFT:
                return [$this->channel, $username, $giftSubCount, $methods, $userState];
            default:
                throw new DomainException('Unhandled USERNOTICE: ' . $msgId);
        }
    }
}
