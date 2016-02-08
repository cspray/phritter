<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace Cspray\Phritter\Authorization;

class AppCredentials {

    private $consumerKey;
    private $consumerSecret;

    public function __construct(string $consumerKey, string $consumerSecret) {
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
    }

    public function consumerKey() : string {
        return $this->consumerKey;
    }

    public function consumerSecret() : string {
        return $this->consumerSecret;
    }

}