<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace Cspray\Phritter;

use Cspray\Phritter\Authorization\OauthHeaderGenerator;
use Amp\Artax\Request;

class FilterTweetStream {

    private $httpClient;

    private $oauthHeaderGenerator;
    private $filterParameters;

    private $filterUrl = 'https://stream.twitter.com/1.1/statuses/filter.json?stall_warnings=true';

    public function __construct(OauthHeaderGenerator $headerGenerator, FilterParameters $filterParameters) {
        $this->httpClient = httpClient();
        $this->oauthHeaderGenerator = $headerGenerator;
        $this->filterParameters = $filterParameters;
    }

    public function getRequest() : Request {
        $request = (new Request())->setUri($this->filterUrl)
                                  ->setMethod('POST')
                                  ->setHeader('Content-Type', 'application/x-www-form-urlencoded')
                                  ->setBody($this->generateBodyContent());

        $authorizedHeader = $this->oauthHeaderGenerator->generateHeader($request);

        $request->setHeader('Authorization', $authorizedHeader);

        return $request;
    }

    private function generateBodyContent() : string {
        $followIds = $this->filterParameters->followIds();
        $keywords = $this->filterParameters->trackKeywords();

        if (empty($followIds) && empty($keywords)) {
            throw new Exception('FilterParameters passed are invalid');
        }

        $params = [];

        if (!empty($followIds)) {
            $params['follow'] = implode(',', $followIds);
        }

        if (!empty($keywords)) {
            $params['track'] = implode(',', $keywords);
        }


        return http_build_query($params);
    }

    public function __toString() : string {
        $className = self::class;
        $body = rawurldecode($this->generateBodyContent());

        return "{$className}\n\n{$body}";
    }

}