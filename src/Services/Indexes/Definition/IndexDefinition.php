<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Definition;

use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\IndexScope;

abstract class IndexDefinition implements IndexDefinitionInterface
{
    protected const int MAX_PAGINATION_LIMIT = 10000;

    public function __construct(
        private readonly IndexMappingInterface $indexMapping,
        private readonly ?IndexScope $scope = IndexScope::Public,
    ) {}

    abstract public static function getIndexType(): string;

    public function getIndexMapping(): IndexMappingInterface
    {
        return $this->indexMapping;
    }

    public function getScope(): ?IndexScope
    {
        return $this->scope;
    }

    public function getPaginationLimit(): int
    {
        return self::MAX_PAGINATION_LIMIT;
    }
}
