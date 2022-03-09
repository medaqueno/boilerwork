#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Clients;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * @desc Simple Wrapper for https://docs.guzzlephp.org/en/7.0/overview.html
 * Requests and response follow PSR guides
 */
class HttpClient implements ClientInterface
{
    public function __construct(private Client $client)
    {
    }

    /**
     * @desc Send request and return response
     * @use $response = $this->client->request(method: 'GET', uri: 'http://httpbin.org/anything?foo=bar');
     */
    public function request(string $method, $uri = 'GET', array $options = []): ResponseInterface
    {
        return $this->client->request(method: $method, uri: $uri, options: $options);
    }

    /**
     * @desc Create Request to be sent with sendRequest method
     * @use $this->client->createRequest(method: 'GET', uri: 'http://httpbin.org/anything?foo=bar');
     */
    public function createRequest(string $method, $uri = 'GET', array $headers = [], $body = null, string $version = '1.1'): RequestInterface
    {
        return new Request(method: $method, uri: $uri, headers: $headers, body: $body, version: $version);
    }

    /**
     * @desc Send created request and return response
     * @use $this->client->sendRequest(request: $request);
     */
    public function sendRequest(RequestInterface $request, array $options = []): ResponseInterface
    {
        return $this->client->send(request: $request, options: $options);
    }
}
