#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System\Http;

use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Request as SwooleRequest;

/**
 * Implements Laminas Diactoros PSR-7 and PSR-17
 * https://docs.laminas.dev/laminas-diactoros/v2/overview/
 **/
class Request extends ServerRequest
{
    public static function createFromSwoole($request): ServerRequestInterface
    {
        return ServerRequestFactory::fromGlobals(
            server: $request->server,
            query: $request->get,
            body: self::parseBody($request),
            cookies: $request->cookie,
            files: $request->files,
        );
    }

    private static function parseBody(SwooleRequest $request): array
    {
        $body = [];
        if (
            ($request->server['request_method'] === 'POST'
                || $request->server['request_method'] === 'PATCH'
                || $request->server['request_method'] === 'PUT'
            )
            && $request->header['content-type'] === 'application/json'
        ) {
            $body = $request->rawContent();
            $body = empty($body) ? [] : json_decode($body);
        } else {
            $body = $request->post ?? [];
        }

        return $body;
    }
}
