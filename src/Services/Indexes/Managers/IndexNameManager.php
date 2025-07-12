<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Managers;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;

class IndexNameManager implements IndexNameManagerInterface
{
    public function __construct(
        private readonly string $indexSeparator,
        private readonly string $indexPrefix,
    ) {}

    public function getIndexSeparator(): string
    {
        return $this->indexSeparator;
    }

    public function getIndexPrefix(): string
    {
        return $this->indexPrefix;
    }

    public function getIndexSuffix(): string
    {
        return date('YmdHis');
    }

    public function getAliasName(string $indexType): string
    {
        return $this->getIndexPrefix().$indexType;
    }

    public function getIndexNameForType(string $indexType, ?string $suffix = null): string
    {
        $suffix ??= $this->getIndexSuffix();

        return $this->getAliasName($indexType).$this->getIndexSeparator().$suffix;
    }

    public function getIndexName(Index $index): string
    {
        return $index->getName() ?? $this->getAliasName($index->getType());
    }
}
