<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace Cspray\Phritter\Test;

use Cspray\Phritter\FilterParameters;
use Cspray\Phritter\FilterTweetStream;
use Cspray\Phritter\Authorization\OauthHeaderGenerator;
use Cspray\Phritter\Exception;
use PHPUnit_Framework_TestCase as UnitTestCase;

class FilterTweetStreamTest extends UnitTestCase {

    private function getMockOauthHeaderGenerator() : OauthHeaderGenerator {
        return $this->getMockBuilder(OauthHeaderGenerator::class)->disableOriginalConstructor()->getMock();
    }

    public function testGeneratingBodyContentHandlesErrors() {
        $headerGenerator = $this->getMockOauthHeaderGenerator();
        $filterParameters = new FilterParameters([], []);
        $tweetStream = new FilterTweetStream($headerGenerator, $filterParameters);

        $expectedMsg = '/FilterParameters passed are invalid/';
        $this->setExpectedExceptionRegExp(Exception::class, $expectedMsg);

        $tweetStream->getRequest();
    }

    public function testGeneratingBodyContentOnlyFollowIds() {
        $headerGenerator = $this->getMockOauthHeaderGenerator();
        $filterParameters = new FilterParameters(['1', '2', '3', '4'], []);
        $tweetStream = new FilterTweetStream($headerGenerator, $filterParameters);

        $expectedOutput = 'follow=1%2C2%2C3%2C4';
        $this->assertSame($expectedOutput, $tweetStream->getRequest()->getBody());
    }

    public function testGeneratingBodyContentOnlyKeywords() {
        $headerGenerator = $this->getMockOauthHeaderGenerator();
        $filterParameters = new FilterParameters([], ['one', 'two', 'three']);
        $tweetStream = new FilterTweetStream($headerGenerator, $filterParameters);

        $expectedOutput = 'track=one%2Ctwo%2Cthree';
        $this->assertSame($expectedOutput, $tweetStream->getRequest()->getBody());
    }

    public function testGeneratingBodyContentBothFollowersAndKeywords() {
        $headerGenerator = $this->getMockOauthHeaderGenerator();
        $filterParameters = new FilterParameters(['1', '2', '3', '4'], ['one', 'two foo', 'three']);
        $tweetStream = new FilterTweetStream($headerGenerator, $filterParameters);

        $expectedOutput = 'follow=1%2C2%2C3%2C4&track=one%2Ctwo+foo%2Cthree';
        $this->assertSame($expectedOutput, $tweetStream->getRequest()->getBody());
    }
}