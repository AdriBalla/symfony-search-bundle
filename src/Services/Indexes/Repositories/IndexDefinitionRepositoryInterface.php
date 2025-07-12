<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Repositories;

use Adriballa\SymfonySearchBundle\Services\Indexes\Definition\IndexDefinitionInterface;

interface IndexDefinitionRepositoryInterface
{
    public function getIndexDefinition(string $indexType): IndexDefinitionInterface;

    public function indexDefinitionExists(string $indexType): bool;
}
