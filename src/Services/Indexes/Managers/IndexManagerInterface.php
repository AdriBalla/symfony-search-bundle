<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Managers;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\IndexMapping;
use Adriballa\SymfonySearchBundle\Services\Indexes\Setting\IndexSettings;

interface IndexManagerInterface
{
    public function indexExists(Index $index): bool;

    public function createIndex(IndexMapping $indexMapping, ?string $suffix = null): Index;

    public function copyIndex(string $indexFrom, string $indexTo, bool $wait = true): ?string;

    public function addAliasOnIndex(Index $index, bool $deleteOld = false): void;

    public function setIndexSetting(
        Index $index,
        IndexSettings $settings,
    ): void;

    public function refreshIndex(Index $index): void;

    public function deleteIndex(Index $index): bool;
}
