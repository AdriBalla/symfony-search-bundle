<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Client;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;

interface IndexClientInterface
{
    public function createIndex(string $indexType, bool $addAlias = false, bool $deleteExisting = false): Index;

    public function deleteIndex(string $indexType): void;

    public function indexExists(Index $index): bool;

    public function copyIndex(Index $index, bool $addAlias, bool $deleteExisting): Index;
}
