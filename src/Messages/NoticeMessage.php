<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Events\Twitch\EmoteOnlyModeEvent;
use GhostZero\Tmi\Events\Twitch\ModsEvent;
use GhostZero\Tmi\Events\Twitch\NoticeEvent;
use GhostZero\Tmi\Events\Twitch\R9kModeEvent;
use GhostZero\Tmi\Events\Twitch\SubsOnlyModeEvent;
use GhostZero\Tmi\Events\Twitch\VipsEvent;

class NoticeMessage extends IrcMessage
{
    public const TAG_EMOTEONLY_ON = 'emote_only_on';
    public const TAG_EMOTEONLY_OFF = 'emote_only_off';
    public const TAG_NO_MODS = 'no_mods';
    public const TAG_NO_VIPS = 'no_vips';
    public const TAG_R9K_MODE_OFF = 'r9k_off';
    public const TAG_R9K_MODE_ON = 'r9k_on';
    public const TAG_ROOM_MODS = 'room_mods';
    public const TAG_SUBMODE_OFF = 'subs_off';
    public const TAG_SUBMODE_ON = 'subs_on';
    public const TAG_VIPS = 'vips_success';

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
        }

        $msgId = $this->tags['msg-id'] ?? '';
        $events = [
            new NoticeEvent($this->channel, $msgId),
        ];

        if (($event = $this->getSpecificEvent($msgId))) {
            $events[] = $event;
        }

        return $events;
    }

    public function getSpecificEvent(string $msgId): ?Event
    {
        switch ($msgId) {
            case self::TAG_EMOTEONLY_OFF:
                return new EmoteOnlyModeEvent($this->channel, false);
            case self::TAG_EMOTEONLY_ON:
                return new EmoteOnlyModeEvent($this->channel, true);
            case self::TAG_NO_MODS:
                return new ModsEvent($this->channel, []);
            case self::TAG_NO_VIPS:
                return new VipsEvent($this->channel, []);
            case self::TAG_R9K_MODE_OFF:
                return new R9kModeEvent($this->channel, false);
            case self::TAG_R9K_MODE_ON:
                return new R9kModeEvent($this->channel, true);
            case self::TAG_ROOM_MODS:
                $mods = array_filter(explode(', ', strtolower(explode(': ', $this->message)[1])), static fn($n) => $n);
                return new ModsEvent($this->channel, $mods);
            case self::TAG_SUBMODE_OFF:
                return new SubsOnlyModeEvent($this->channel, false);
            case self::TAG_SUBMODE_ON:
                return new SubsOnlyModeEvent($this->channel, true);
            case self::TAG_VIPS:
                if (substr($this->message, -strlen($this->message)) === '.') {
                    $this->message = substr($this->message, 0, -1);
                }
                $vips = array_filter(explode(', ', strtolower(explode(': ', $this->message)[1])), static fn($n) => $n);

                return new VipsEvent($this->channel, $vips);
            default:
                return null;
        }
    }
}
