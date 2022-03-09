#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Clients;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * @desc Simple Wrapper for https://docs.guzzlephp.org/en/7.0/overview.html
 * Requests and response follow PSR guides
 */
class HttpClient implements ClientInterface
{
    private function __construct(private Client $client)
    {
    }

    /**
     * @desc Send request and return response
     * @example $response = $this->client->request(method: 'GET', uri: 'http://httpbin.org/anything?foo=bar');
     */
    public function request(string $method, string|UriInterface $uri = 'GET', array $options = []): ResponseInterface
    {
        return $this->client->request(method: $method, uri: $uri, options: $options);
    }

    /**
     * @desc Create Request to be sent with sendRequest method
     *
     * @param string                               $method  HTTP method
     * @param string|UriInterface                  $uri     URI
     * @param array<string, string|string[]>       $headers Request headers
     * @param string|StreamInterface|null $body    Request body
     * @param string                               $version Protocol version
     *
     * @example $this->client->createRequest(method: 'GET', uri: 'http://httpbin.org/anything?foo=bar');
     */
    public function createRequest(string $method, string|UriInterface $uri = 'GET', array $headers = [], string|StreamInterface|null $body = null, string $version = '1.1'): RequestInterface
    {
        return new Request(method: $method, uri: $uri, headers: $headers, body: $body, version: $version);
    }

    /**
     * @desc Send created request and return response
     * @example $this->client->sendRequest(request: $request);
     */
    public function sendRequest(RequestInterface $request, array $options = []): ResponseInterface
    {
        return $this->client->send(request: $request, options: $options);
    }
}
