<?php

namespace GhostZero\Tmi\Messages;

use Error;
use Generator;
use GhostZero\Tmi\Exceptions\ParseException;
use GhostZero\Tmi\Tags;
use Throwable;

class IrcMessageParser
{

    /**
     * @param string $message raw message
     * @return Generator|IrcMessage[]|null
     * @throw ParseException
     */
    public function parse(string $message): ?Generator
    {
        foreach (explode("\n", $message) as $msg) {
            if (empty(trim($msg))) {
                continue;
            }

            yield $this->parseSingle($msg);
        }
    }

    /**
     * @param string $message
     * @return IrcMessage
     * @throw ParseException
     */
    public function parseSingle(string $message): IrcMessage
    {
        try {
            [$tags, $message] = $this->parseTags($message);

            switch ($this->getCommand($message)) {
                case 'KICK':
                    $msg = new KickMessage($message);
                    break;

                case 'PING':
                    $msg = new PingMessage($message);
                    break;

                case 'PRIVMSG':
                case 'WHISPER':
                    $msg = new PrivmsgMessage($message);
                    break;

                case IrcCommand::RPL_WELCOME:
                    $msg = new WelcomeMessage($message);
                    break;

                case 'TOPIC':
                case IrcCommand::RPL_TOPIC:
                    $msg = new TopicChangeMessage($message);
                    break;

                case IrcCommand::RPL_NAMREPLY:
                    $msg = new NameReplyMessage($message);
                    break;

                case IrcCommand::RPL_MOTD:
                    $msg = new MotdMessage($message);
                    break;
                case 'USERNOTICE':
                    $msg = new UserNoticeMessage($message);
                    break;
                case 'NOTICE':
                    $msg = new NoticeMessage($message);
                    break;
                case 'ROOMSTATE':
                    $msg = new RoomStateMessage($message);
                    break;
                case 'HOSTTARGET':
                    $msg = new HostTargetMessage($message);
                    break;
                case 'JOIN':
                    $msg = new JoinMessage($message);
                    break;
                case 'PART':
                    $msg = new PartMessage($message);
                    break;

                default:
                    $msg = new IrcMessage($message);
                    break;
            }

            return $msg->withTags($tags);
        } catch (Throwable $throwable) {
            throw ParseException::fromParseSingle($message, $throwable);
        }
    }

    private function getCommand(string $message): string
    {
        if ($message[0] === ':') {
            $message = trim(strstr($message, ' '));
        }

        return strstr($message, ' ', true);
    }

    private function parseTags(string $message): array
    {
        if ($message[0] !== '@') {
            return [new Tags(), $message];
        }

        $data = explode(' ', $message);

        return [
            Tags::parse(ltrim($data[0], '@')),
            implode(' ', array_slice($data, 1)),
        ];
    }
}
