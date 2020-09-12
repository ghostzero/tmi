# PHP Twitch Messaging Interface

Inspired by [tmi.js](https://github.com/tmijs/tmi.js), [php-irc-client](https://github.com/jerodev/php-irc-client) this package is a full featured, high performance Twitch IRC client written in PHP 7.4.

Also have a look at [ghostzero/tmi-cluster](https://github.com/ghostzero/tmi-cluster). TMI Cluster is a Laravel package that makes the PHP TMI client scalable.

## Features

- Connecting to Twitch IRC with SSL
- Generic IRC Commands
- Supports Twitch IRC Tags (IRC v3)
- Supports Twitch IRC Membership
- Supports Twitch IRC Commands

## Getting Started (w/o OAuth Token)

```php
use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Client;
use GhostZero\Tmi\ClientOptions;
use GhostZero\Tmi\Tags;

$client = new Client(new ClientOptions([
    'connection' => [
        'secure' => true,
        'reconnect' => true,
        'rejoin' => true,
    ],
    'channels' => ['ghostzero']
]));

$client->on('message', function (Channel $channel, Tags $tags, string $user, string $message, bool $self) use ($client) {
    print "{$tags['display-name']}: {$message}";
});

$client->connect();
```

## Getting Started (w/ OAuth Token)

```php
use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Client;
use GhostZero\Tmi\ClientOptions;
use GhostZero\Tmi\Tags;

$client = new Client(new ClientOptions([
    'options' => ['debug' => true],
    'connection' => [
        'secure' => true,
        'reconnect' => true,
        'rejoin' => true,
    ],
    'identity' => [
        'username' => 'ghostzero',
        'password' => 'oauth:...',
    ],
    'channels' => ['ghostzero']
]));

$client->on('message', function (Channel $channel, Tags $tags, string $user, string $message, bool $self) use ($client) {
    if ($self) return;

    if (strtolower($message) === '!hello') {
        $client->say($channel->getName(), "@{$user}, heya!");
    }
});

$client->connect();
```
