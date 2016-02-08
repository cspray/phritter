<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

require_once __DIR__ . '/vendor/autoload.php';

use Cspray\Phritter\Authorization\OauthHeaderGenerator;
use Cspray\Phritter\FilterParameters;
use Cspray\Phritter\FilterTweetStream;
use Cspray\Phritter\TwitterUserId;

$credentials = json_decode(file_get_contents( __DIR__ . '/.phritter-credentials.json'), true);

$oauthHeaderGenerator = OauthHeaderGenerator::createFromConsumerAndOauthCredentials(
    $credentials['consumer_key'],
    $credentials['consumer_secret'],
    $credentials['oauth_token'],
    $credentials['oauth_secret']
);

$userId = new TwitterUserId($oauthHeaderGenerator);

$filterParameters = new FilterParameters(
    [$userId->fetch('charlesspray')],         // follower IDs
    ['cam newton']          // keywords to track
);

$stream = new FilterTweetStream($oauthHeaderGenerator, $filterParameters);

$app = new \Cspray\Phritter\Application($stream);
$app->run();