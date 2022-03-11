#!/usr/bin/env php
<?php

declare(strict_types=1);

function app(): \Bootstrap\Application
{
    return \Bootstrap\Application::getInstance();
}

/**
 * @return string Path from /src
 **/
function base_path(string $path): string
{
    return __DIR__ . '/../..' . $path;
}

function logger(string|Stringable|array $message, string $mode = 'DEBUG', string $channel = 'default'): void
{
    $d = new DateTimeImmutable();

    $message = is_array($message) ? json_encode($message) : ((method_exists($message, '__toString')) ? $message->__toString() : $message);
    $message = '[' . $d->format(DateTime::ATOM) . '] ' . strtoupper($mode) . ' ' . $message . PHP_EOL;

    $fp = fopen(base_path('/logs/') . $channel . '_' . $d->format('Y-m-d') . '.log', 'a');
    stream_set_blocking($fp, false);

    if (flock($fp, LOCK_EX)) {
        fwrite($fp, $message);
    }
    flock($fp, LOCK_UN);
    fclose($fp);
}

function response_empty(int $statusCode = 200): array
{
    return response(null, $statusCode);
}

function response(mixed $response = null, int $statusCode = 200): array
{
    return [
        'data' => !empty($response) || !is_null($response) ? json_encode($response) : null,
        'statusCode' => $statusCode,
    ];
}

// Monitor memory
function getMemoryStatus(): void
{
    $fh = fopen('/proc/meminfo', 'r');

    if ($fh) {
        $memTotal = 0;
        while ($line = fgets($fh)) {

            $pieces = array();

            if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
                $memTotal = $pieces[1];
                break;
            }
        }
        fclose($fh);

        printf("\n Memory Total: %s kB\n Memory Usage: %s kB\n Memory Peak: %s kB\n\n", $memTotal, memory_get_usage() / 1024, memory_get_peak_usage(true) / 1024);
    }
}
