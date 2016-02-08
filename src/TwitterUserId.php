<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace Cspray\Phritter;

use Cspray\Phritter\Authorization\OauthHeaderGenerator;
use Amp\Artax\Request;
use Amp\Artax\Response;
use function Amp\wait;
use function Cspray\Phritter\httpClient;

class TwitterUserId {

    private $oauthHeaderGenerator;

    public function __construct(OauthHeaderGenerator $headerGenerator) {
        $this->oauthHeaderGenerator = $headerGenerator;
    }

    public function fetch(string $screenName) {
        $params = [
            'screen_name' => $screenName,
            'include_entities' => 'false'
        ];
        $request = (new Request())->setMethod('GET')->setUri('https://api.twitter.com/1.1/users/show.json?' . http_build_query($params));

        $authorizedHeader = $this->oauthHeaderGenerator->generateHeader($request);

        $request->setHeader('Authorization', $authorizedHeader);

        $responsePromise = httpClient()->request($request);

        /** @var Response $response */
        // TODO handle scenario when reactor is already running when this is called
        $response = wait($responsePromise);

        if ($response->getStatus() === 200) {
            $data = json_decode($response->getBody(), true);
            return $data['id_str'];
        } else {
            return false;
        }
    }

}