#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\ExampleBoundedContext\UI\ViewModels;

use App\Core\ExampleBoundedContext\Infra\Mappers\ExampleMapper;
use Boilerwork\Infra\Persistence\Adapters\PostgreSQL\PostgreSQLReadsClient;
use Boilerwork\System\Http\AbstractHttpViewModelPort;
use Boilerwork\System\Http\Request;
use Boilerwork\System\Http\Response;
use Psr\Http\Message\ResponseInterface;

final class ListAllExamplesView extends AbstractHttpViewModelPort
{
    private string $projectionName = 'all_examples';

    public function __construct(
        private PostgreSQLReadsClient $client
    ) {
    }

    public function __invoke(Request $request, array $vars): ResponseInterface
    {
        $this->client->getConnection();
        $result = $this->client->fetchAll('SELECT * FROM "' . $this->projectionName . '" ORDER BY id');
        $this->client->putConnection();

        $response = array_map(fn ($item): array => ExampleMapper::map($item), $result);

        return Response::json($response);
    }
}
