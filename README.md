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
use GhostZero\Tmi\Client;
use GhostZero\Tmi\ClientOptions;
use GhostZero\Tmi\Events\Twitch\MessageEvent;

$client = new Client(new ClientOptions([
    'connection' => [
        'secure' => true,
        'reconnect' => true,
        'rejoin' => true,
    ],
    'channels' => ['ghostzero']
]));

$client->on(MessageEvent::class, function (MessageEvent $e) {
    print "{$e->tags['display-name']}: {$e->message}";
});

$client->connect();
```

## Getting Started (w/ OAuth Token)

```php
use GhostZero\Tmi\Client;
use GhostZero\Tmi\ClientOptions;
use GhostZero\Tmi\Events\Twitch\MessageEvent;

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

$client->on(MessageEvent::class, function (MessageEvent $e) use ($client) {
    if ($e->self) return;

    if (strtolower($e->message) === '!hello') {
        $client->say($e->channel->getName(), "@{$e->user}, heya!");
    }
});

$client->connect();
```
