# PHP Twitch Messaging Interface

<a href="https://packagist.org/packages/ghostzero/tmi"><img src="https://img.shields.io/packagist/dt/ghostzero/tmi" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/ghostzero/tmi"><img src="https://img.shields.io/packagist/v/ghostzero/tmi" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/ghostzero/tmi"><img src="https://img.shields.io/packagist/l/ghostzero/tmi" alt="License"></a>
<a href="https://discord.gg/qsxVMNg"><img src="https://discordapp.com/api/guilds/552952675369484301/embed.png?style=shield" alt="Discord"></a>
<a href="https://app.fossa.com/projects/git%2Bgithub.com%2Fghostzero%2Ftmi?ref=badge_shield" alt="FOSSA Status"><img src="https://app.fossa.com/api/projects/git%2Bgithub.com%2Fghostzero%2Ftmi.svg?type=shield"/></a>

## Introduction

Inspired by [tmi.js](https://github.com/tmijs/tmi.js) and [php-irc-client](https://github.com/jerodev/php-irc-client) this package is a full featured, high performance Twitch IRC client written in PHP 7.4.

Also have a look at [ghostzero/tmi-cluster](https://github.com/ghostzero/tmi-cluster). TMI Cluster is a Laravel package that makes the PHP TMI client scalable.

## Features

- Connecting to Twitch IRC with SSL
- Generic IRC Commands
- Supports Twitch IRC Tags (IRC v3)
- Supports Twitch IRC Membership
- Supports Twitch IRC Commands

## Official Documentation

You can view our official documentation [here](https://tmiphp.com/docs/).

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


## License
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fghostzero%2Ftmi.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2Fghostzero%2Ftmi?ref=badge_large)