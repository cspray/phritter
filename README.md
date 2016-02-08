# Phritter

> This library is still under development and may change or suffer from horrendous, space-time-continuum-shredding bugs.

A library to asynchronously gather and store data from Twitter's streaming API for future processing.

## Installation

Phritter requires PHP 7. Please check out the [PHP home page](https://php.net) for installation instructions.

To install Phritter we recommend using [Composer]().

```
{
    "require": {
        "cspray/phritter": "dev-master"
    }
}
```

## Quick Start

Phritter assumes that you have created a Twitter app and have collected your consumer key and secret. Additionally you 
should pre-generate an OAuth token and secret using Twitter's developer interface. This library is not intended to generate 
OAuth tokens and will not work unless valid credentials are provided.


There are three steps that need to be completed: 'Authorization', 'Configuring your Stream', 'Start the App'

```
<?php

declare('strict_types=1');

use Cspray\Phritter\Application;
use Cspray\Phritter\FilterParameters;
use Cspray\Phritter\FilterTweetStream;
use Cspray\Phritter\OauthHeaderGenerator;
use Cspray\Phritter\TwitterUserId;

// Authorization

// This step ensures that we can produce the appropriate OAuth header as all API requests require authentication. We highly 
// recommend that you store the Consumer and OAuth credentials in a file not in source control. This example assumes a simple 
// JSON file with 4 keys that map to the credentials.

$credentials = json_decode(file_get_contents(__DIR__ . '/.phritter-credentials.json'), true);
$oauthGenerator = new OauthHeaderGenerator::createFromConsumerAndOauthCredentials(
    $credentials['consumer_key'],
    $credentials['consumer_secret'],
    $credentials['oauth_token'],
    $credentials['oauth_secret']
);

// Configure your Stream

$userId = new TwitterUserId($oauthGenerator);   // Convenience utility that allows you to fetch a Twitter user's ID via their screen_name
$filterParams = new FilterParameters(
    [$userId->fetch('charlesspray')],  // array of user IDs to follow
    ['async php']                      // keywords to track
);
$filterStream = new FilterTweetStream($oauthGenerator, $filterParams);

// Provide app dependencies and run it!
$storage = weHaveNotImplementedThisYet();
$log = new Monolog\Logger();        // optional - or any PSR-3 compliant logger, Monolog comes out-of-the-box
$app = new Application($filterStream, $storage, $log);

$app->run();
```

## TODO Before 1.0 release

- [ ] Add support for stall checking per Twitter's documentation
- [ ] Smartly handle stall warnings sent from Twitter's API.
- [ ] Implement a reconnecting and backoff strategy per Twitter's documentation
- [ ] Support remaining filter options on Cspray\Phritter\FilterTweetStream
- [ ] Smartly handle common errors returned from Twitter's streaming API
- [ ] Handle chunked data appropriately to be able to handle compression decoding
- [ ] Implement storage interface and at least 2 implementations: 'FileStorage', 'PgStorage'
- [ ] Implement a SampleTweetStream.
- [ ] Implement unit tests for everything feasible.
- [ ] Implement a CHANGELOG and CONTRIBUTING for initial release