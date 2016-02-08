<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace Cspray\Phritter;

use Amp\Artax\Client as HttpClient;

function httpClient() : HttpClient {
    static $client;

    if (!isset($client)) {
        $client = new HttpClient();
        $client->setAllOptions([
            HttpClient::OP_DEFAULT_USER_AGENT => Application::USER_AGENT,
            HttpClient::OP_VERBOSITY => HttpClient::VERBOSE_NONE,
            HttpClient::OP_AUTO_ENCODING => false
        ]);
    }

    return $client;
}
