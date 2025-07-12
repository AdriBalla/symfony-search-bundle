<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Stubs;

use Adriballa\SymfonySearchBundle\Services\Indexes\Definition\IndexDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\IndexScope;

class TestIndexDefinition implements IndexDefinitionInterface
{
    public static function getIndexType(): string
    {
        return 'test_mocks';
    }

    public function getIndexMapping(): IndexMappingInterface
    {
        return new TestIndexMapping();
    }

    public function getScope(): ?IndexScope
    {
        return IndexScope::Public;
    }

    public function getPaginationLimit(): int
    {
        return 10000;
    }
}
