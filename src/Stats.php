<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace Cspray\Phritter;


class Stats {

    private $startTime;
    private $counts = [
        'connectionAttempts' => 0,
        'statusUpdates' => 0
    ];

    public function streamEstablished() {
        $this->startTime = time();
    }

    public function connectionAttempts() : int {
        return $this->counts['connectionAttempts'];
    }

    public function statusUpdates() : int {
        return $this->counts['statusUpdates'];
    }

    public function increment(string $countToIncrement) {
        $this->counts[$countToIncrement]++;
    }

    public function startTime() : int {
        return $this->startTime;
    }

}