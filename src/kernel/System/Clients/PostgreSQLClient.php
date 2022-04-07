#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System\Clients;

use Swoole\Coroutine\PostgreSQL;

/**
 * Client to PostgreSQL
 *
 * Get a connection from connection pool in order to work with.
 * Don't forget to put the connection back to the pool
 *
 * @example
        $pgClient = new PostgreSQLClient();
        $query = $pgClient->run('select * from events where "aggregateId" = $1', ["1e1ec9be-2c19-48c5-9580-5de4088cbcf6"]);
        $arr = $pgClient->fetchAll($query);
        var_dump($arr);
        $pgClient->putConnection($pgClient->conn);
 *
 **/
class PostgreSQLClient
{
    public readonly PostgreSQL $conn;

    private readonly PostgreSQLPool $instance;

    public function __construct()
    {
        $this->instance = PostgreSQLPool::getInstance();
        $this->conn = $this->instance->getConn();
    }

    /**
     * Put connection back to the pool in order to be reused
     **/
    public function putConnection(PostgreSQL $conn): void
    {
        $this->instance->putConn($conn);
    }

    /**
     * @description Run Query, and prepare/execute it automatically if includes args
     * @example $query = $pgClient->run('select * from events where "aggregateId" = $1', ["1e1ec9be-2c19-48c5-9580-5de4088cbcf6"]);
     **/
    public function run(string $query, array $args = [])
    {
        if (!$args) {
            return $this->query($query);
        }

        $queryName = $this->prepare($query);

        return $this->execute($queryName, $args);
    }

    private function query(string $query): mixed
    {
        return $this->conn->query($query);
    }

    public function fetchAll($result): array
    {
        $resp = [];
        while ($row = $this->conn->fetchRow($result)) {
            $resp[] = $row;
        }

        return $resp;
    }

    private function prepare(string $query): string
    {
        $queryName = base64_encode($query);
        $this->conn->prepare($queryName, $query);

        return $queryName;
    }

    private function execute(string $queryName, array $values)
    {
        return $this->conn->execute($queryName, $values);
    }

    public function initTransaction(): void
    {
        $this->conn->query('BEGIN');
    }

    public function endTransaction(): void
    {
        $this->conn->query('COMMIT');
    }
}
