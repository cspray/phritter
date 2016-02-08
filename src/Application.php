<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace Cspray\Phritter;

use Amp\Artax\Notify;
use Amp\Artax\Request;
use Amp\Artax\Client as HttpClient;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use function Amp\repeat;
use function Amp\reactor;

class Application {

    const USER_AGENT = 'Phritter v0.1.0-alpha';

    private $logger;
    private $tweetStream;

    public function __construct(FilterTweetStream $tweetStream, LoggerInterface $logger = null) {
        $this->tweetStream = $tweetStream;
        $this->logger = $logger ?? new Logger('phritter', [new StreamHandler(STDOUT)]);
    }

    public function run() {
        $cb = function() {
            $this->logger->info('Starting to process ' . $this->tweetStream);
            $this->connect($this->tweetStream->getRequest());
        };
        $cb = $cb->bindTo($this, $this);

        reactor()->run($cb);
    }

    private function connect(Request $request) {
        $promise = httpClient()->request($request, [HttpClient::OP_DISCARD_BODY => true, HttpClient::OP_MS_TRANSFER_TIMEOUT => null]);
        // yes, yes not spec compliant but it *gets the job done* until we can implement something better
        $chunkCombiner = new JsonChunkCombiner();
        $stats = new Stats();
        $logger = $this->logger;

        $callback = function(array $notifyData) use($chunkCombiner, $stats, $logger) {
            $event = array_shift($notifyData);

            if ($event === Notify::REQUEST_SENT) {
                $stats->increment('connectionAttempts');
                $msg = "Connection request #{$stats->connectionAttempts()} sent";
                $logger->info($msg);
            }

            if ($event === Notify::RESPONSE_HEADERS) {
                $stats->streamEstablished();
                $prettyTime = date('Y-m-d H:i:s', $stats->startTime());
                $logger->info("Twitter responded at {$prettyTime} UTC");
            }

            if ($event === Notify::RESPONSE_BODY_DATA) {
                $data = trim($notifyData[0]);
                if (!empty($data)) {
                    $chunkCombiner->push($data);
                    if ($chunkCombiner->isValidJson()) {
                        // TODO pass $json to Storage when implemented
                        $json = $chunkCombiner->getCombinedChunksAsJson();
                        $chunkCombiner->clearChunks();

                        $stats->increment('statusUpdates');
                    }
                }
            }
        };

        $promise->watch($callback);

        $promise->when(function($err, $response) use($logger) {
            if ($err) {
                $exceptionType = get_class($err);
                $msg = "{$exceptionType}('{$err->getMessage()}') in {$err->getFile()} on L{$err->getLine()}";
                $logger->critical($msg);
                // TODO if an exception was thrown should decide if we want to stop execution; this should probably not happen in normal circumstances
            } else {
                $msg = "Connection ended: {$response->getStatus()} {$response->getReason()}";
                $logger->error($msg);
            }
            // TODO remove this when we have the reconnection/backoff implemented
            \Amp\stop();
        });

        $statUpdate = function() use($stats) {
            $runningTime = time() - $stats->startTime();
            $this->logger->info("Stats: {$stats->statusUpdates()} statuses. Running for {$runningTime} seconds.");
        };
        $statUpdate = $statUpdate->bindTo($this, $this);
        repeat($statUpdate, 15000);
    }

}