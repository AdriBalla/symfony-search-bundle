<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Managers;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;

interface IndexNameManagerInterface
{
    public function getIndexSeparator(): string;

    public function getIndexPrefix(): string;

    public function getIndexSuffix(): string;

    public function getAliasName(string $indexType): string;

    public function getIndexNameForType(string $indexType, ?string $suffix = null): string;

    public function getIndexName(Index $index): string;
}
