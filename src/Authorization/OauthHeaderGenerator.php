<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace Cspray\Phritter\Authorization;

use Amp\Artax\Request;

class OauthHeaderGenerator {

    private $appCredentials;
    private $oauthCredentials;

    public function __construct(AppCredentials $appCredentials, OauthCredentials $oauthCredentials) {
        $this->appCredentials = $appCredentials;
        $this->oauthCredentials = $oauthCredentials;
    }

    public static function createFromConsumerAndOauthCredentials(string $consumerKey, string $consumerSecret, string $oauthToken, string $oauthSecret) : OauthHeaderGenerator {
        $appCreds = new AppCredentials($consumerKey, $consumerSecret);
        $oauthCreds = new OauthCredentials($oauthToken, $oauthSecret);

        return new self($appCreds, $oauthCreds);
    }

    public function generateHeader(Request $request) : string {
        return $this->generateAuthorizationHeader($request);
    }

    private function generateAuthorizationHeader(Request $request) {
        $oauth = [
            'oauth_consumer_key' => $this->appCredentials->consumerKey(),
            'oauth_nonce' => md5(random_bytes(32)),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => (string) time(),
            'oauth_version' => '1.0A',
            'oauth_token' => $this->oauthCredentials->accessToken()
        ];

        $encodedParams = [];
        foreach (array_merge($oauth, $this->getParameters($request)) as $k => $v) {
            $encodedParams[$this->encodeRfc3986($k)] = $this->encodeRfc3986($v);
        }

        ksort($encodedParams);

        $queryParams = [];
        foreach ($encodedParams as $k => $v) {
            $queryParams[] = "$k=$v";
        }

        $encodedMethod = $this->encodeRfc3986(strtoupper($request->getMethod() ?? 'GET'));
        $encodedUrl = $this->encodeRfc3986($this->getNormalizedUrl($request));
        $encodedParameters = $this->encodeRfc3986(implode('&', $queryParams));

        $signatureBase = "{$encodedMethod}&{$encodedUrl}&{$encodedParameters}";

        $key = $this->encodeRfc3986($this->appCredentials->consumerSecret()) . '&' . $this->encodeRfc3986($this->oauthCredentials->accessSecret());

        $signature = base64_encode(hash_hmac('sha1', $signatureBase, $key, true));

        $oauth['oauth_signature'] = $this->encodeRfc3986($signature);

        $output = 'OAuth realm="",';
        foreach ($oauth as $k => $v) {
            $output .= "{$k}=\"{$v}\",";
        }
        $output = trim($output, ',');

        return $output;
    }

    private function encodeRfc3986(string $input) : string {
        return rawurlencode($input);
    }

    private function getParameters(Request $request) {
        $uri = $request->getUri();

        $queryParamStr = parse_url($uri, PHP_URL_QUERY) ?? '';
        parse_str($queryParamStr, $queryParams);

        $body = $request->getBody() ?? '';
        parse_str($body, $bodyParams);

        return array_merge($queryParams, $bodyParams);
    }

    private function getNormalizedUrl(Request $request) {
        $uri = $request->getUri();

        $parsedUrl = parse_url($uri);

        return sprintf(
            '%s://%s%s',
            $parsedUrl['scheme'],
            $parsedUrl['host'],
            $parsedUrl['path']
        );
    }

}