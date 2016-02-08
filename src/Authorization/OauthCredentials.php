<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace Cspray\Phritter\Authorization;


class OauthCredentials {

    private $accessToken;
    private $accessSecret;

    public function __construct(string $accessToken, string $accessSecret) {
        $this->accessToken = $accessToken;
        $this->accessSecret = $accessSecret;
    }

    public function accessToken() : string {
        return $this->accessToken;
    }

    public function accessSecret() : string {
        return $this->accessSecret;
    }

}