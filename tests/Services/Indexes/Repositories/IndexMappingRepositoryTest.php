<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Repositories;

use Adriballa\SymfonySearchBundle\Services\Indexes\Definition\IndexDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\IndexMapping;
use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Repositories\IndexDefinitionRepository;
use Adriballa\SymfonySearchBundle\Services\Indexes\Repositories\IndexMappingRepository;
use Adriballa\SymfonySearchBundle\Services\Indexes\Transformers\IndexMappingTransformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexMappingRepository::class)]
class IndexMappingRepositoryTest extends TestCase
{
    private IndexDefinitionRepository&MockObject $indexDefinitionRepository;
    private IndexMappingTransformer&MockObject $transformer;
    private IndexMappingRepository $indexMappingRepository;

    public function setUp(): void
    {
        $this->indexDefinitionRepository = $this->createMock(IndexDefinitionRepository::class);

        $this->transformer = $this->createMock(IndexMappingTransformer::class);

        $this->indexMappingRepository = new IndexMappingRepository($this->indexDefinitionRepository, $this->transformer);
    }

    public function testGetIndexMapping(): void
    {
        $indexType = 'mocks';
        $configuration = ['this is the configuration'];

        $indexDefinition = $this->createMock(IndexDefinitionInterface::class);
        $indexMapping = $this->createMock(IndexMappingInterface::class);

        $this->indexDefinitionRepository->expects($this->once())
            ->method('getIndexDefinition')
            ->with($indexType)
            ->willReturn($indexDefinition)
        ;

        $indexDefinition->expects($this->once())
            ->method('getIndexMapping')
            ->willReturn($indexMapping)
        ;

        $this->transformer->expects($this->once())
            ->method('getElasticsearchConfiguration')
            ->with($indexMapping)
            ->willReturn($configuration)
        ;

        $expected = new IndexMapping($indexType, $configuration);

        $this->assertEquals($expected, $this->indexMappingRepository->getIndexMapping($indexType));
    }
}
