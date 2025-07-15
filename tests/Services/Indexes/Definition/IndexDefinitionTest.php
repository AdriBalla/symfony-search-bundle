<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Definition;

use Adriballa\SymfonySearchBundle\Services\Indexes\Definition\IndexDefinition;
use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\IndexScope;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexDefinition::class)]
class IndexDefinitionTest extends TestCase
{
    /**
     * @dataProvider scopeDataProvider
     */
    public function testIndexDefinition(IndexScope $scope): void
    {
        $indexMapping = $this->createMock(IndexMappingInterface::class);
        $indexDefinition = new TestIndexDefinition($indexMapping, $scope);

        $this->assertEquals($indexMapping, $indexDefinition->getIndexMapping());
        $this->assertEquals($scope, $indexDefinition->getScope());
        $this->assertEquals(10000, $indexDefinition->getPaginationLimit());
    }

    /**
     * @return mixed[]
     */
    public static function scopeDataProvider(): array
    {
        return [
            'public' => [
                'scope' => IndexScope::Public,
            ],
            'private' => [
                'scope' => IndexScope::Private,
            ],
        ];
    }
}
