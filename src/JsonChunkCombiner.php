<?php

declare(strict_types = 1);

/**
 * Converts chunks of JSON data into a valid representation of the combined chunks.
 *
 * @license See LICENSE file in project root
 */

namespace Cspray\Phritter;


class JsonChunkCombiner {

    private $chunks = '';
    private $json = [];

    public function clearChunks() {
        $this->chunks = '';
        $this->json = [];
    }

    public function push(string $chunk) {
        $this->chunks .= $chunk;
    }

    public function isValidJson() : bool {
        $this->json = json_decode($this->chunks, true);

        return !empty($this->chunks) && json_last_error() === JSON_ERROR_NONE;
    }

    public function getCombinedChunks() : string {
        return $this->chunks;
    }

    public function getCombinedChunksAsJson() : array {
        return $this->json;
    }

}