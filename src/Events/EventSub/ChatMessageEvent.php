<?php

namespace GhostZero\Tmi\Events\EventSub;

use GhostZero\Tmi\Events\Event;

class ChatMessageEvent extends Event
{
    public string $broadcasterUserId;
    public string $broadcasterUserLogin;
    public string $broadcasterUserName;
    public string $chatterUserId;
    public string $chatterUserLogin;
    public string $chatterUserName;
    public string $messageId;
    public string $messageText;
    public string $messageType;
    public string $color;
    public array $badges;
    public array $fragments;
    public ?array $cheer;
    public ?array $reply;
    public ?string $channelPointsCustomRewardId;
    public ?string $sourceBroadcasterUserId;
    public ?string $sourceBroadcasterUserLogin;
    public ?string $sourceBroadcasterUserName;
    public bool $isSourceOnly;

    public function __construct(array $event)
    {
        $this->broadcasterUserId = $event['broadcaster_user_id'];
        $this->broadcasterUserLogin = $event['broadcaster_user_login'];
        $this->broadcasterUserName = $event['broadcaster_user_name'];
        $this->chatterUserId = $event['chatter_user_id'];
        $this->chatterUserLogin = $event['chatter_user_login'];
        $this->chatterUserName = $event['chatter_user_name'];
        $this->messageId = $event['message_id'];
        $this->messageText = $event['message']['text'];
        $this->fragments = $event['message']['fragments'] ?? [];
        $this->messageType = $event['message_type'];
        $this->color = $event['color'] ?? '';
        $this->badges = $event['badges'] ?? [];
        $this->cheer = $event['cheer'] ?? null;
        $this->reply = $event['reply'] ?? null;
        $this->channelPointsCustomRewardId = $event['channel_points_custom_reward_id'] ?? null;
        $this->sourceBroadcasterUserId = $event['source_broadcaster_user_id'] ?? null;
        $this->sourceBroadcasterUserLogin = $event['source_broadcaster_user_login'] ?? null;
        $this->sourceBroadcasterUserName = $event['source_broadcaster_user_name'] ?? null;
        $this->isSourceOnly = $event['is_source_only'] ?? false;
    }
}
