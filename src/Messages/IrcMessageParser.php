<?php

namespace GhostZero\Tmi\Messages;

use Generator;
use GhostZero\Tmi\Tags;

class IrcMessageParser
{

    /**
     * @param string $message raw message
     * @return Generator|IrcMessage[]|null
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

    private function parseSingle(string $message): IrcMessage
    {
        [$tags, $message] = $this->parseTags($message);

        switch ($this->getCommand($message)) {
            case 'KICK':
                $msg = new KickMessage($message);
                break;

            case 'PING':
                $msg = new PingMessage($message);
                break;

            case 'PRIVMSG':
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

            default:
                $msg = new IrcMessage($message);
                break;
        }

        return $msg->withTags($tags);
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
