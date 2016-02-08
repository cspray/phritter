<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace Cspray\Phritter;

class FilterParameters {

    private $followIds;
    private $trackKeywords;

    public function __construct(array $followIds, array $trackKeywords) {
        $this->followIds = $followIds;
        $this->trackKeywords = $trackKeywords;
    }

    public function followIds() : array {
        return $this->followIds;
    }

    public function trackKeywords() : array {
        return $this->trackKeywords;
    }

}