<?php

namespace GhostZero\Tmi\Events\EventSub;

use GhostZero\Tmi\Events\Event;

class ChatNotificationEvent extends Event
{
    public string $broadcasterUserId;
    public string $broadcasterUserLogin;
    public string $broadcasterUserName;
    public string $chatterUserId;
    public string $chatterUserLogin;
    public string $chatterUserName;
    public bool $chatterIsAnonymous;
    public string $color;
    public array $badges;
    public string $systemMessage;
    public string $messageId;
    public string $messageText;
    public array $fragments;

    /**
     * One of: sub, resub, sub_gift, community_sub_gift, gift_paid_upgrade,
     * prime_paid_upgrade, pay_it_forward, raid, unraid, announcement,
     * bits_badge_tier, charity_donation, shared_chat_sub, etc.
     */
    public string $noticeType;

    /**
     * Raw event payload for notice-type-specific fields (sub, resub, raid, etc.)
     */
    public array $payload;

    public function __construct(array $event)
    {
        $this->broadcasterUserId = $event['broadcaster_user_id'];
        $this->broadcasterUserLogin = $event['broadcaster_user_login'];
        $this->broadcasterUserName = $event['broadcaster_user_name'];
        $this->chatterUserId = $event['chatter_user_id'];
        $this->chatterUserLogin = $event['chatter_user_login'];
        $this->chatterUserName = $event['chatter_user_name'];
        $this->chatterIsAnonymous = $event['chatter_is_anonymous'] ?? false;
        $this->color = $event['color'] ?? '';
        $this->badges = $event['badges'] ?? [];
        $this->systemMessage = $event['system_message'] ?? '';
        $this->messageId = $event['message_id'];
        $this->messageText = $event['message']['text'] ?? '';
        $this->fragments = $event['message']['fragments'] ?? [];
        $this->noticeType = $event['notice_type'];
        $this->payload = $event;
    }

    public function getNoticeData(): ?array
    {
        return $this->payload[$this->noticeType] ?? null;
    }
}
