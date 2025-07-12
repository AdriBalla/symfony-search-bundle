<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Client;

use Adriballa\SymfonySearchBundle\Services\Indexes\Client\IndexClient;
use Adriballa\SymfonySearchBundle\Services\Indexes\Definition\IndexDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\IndexMapping;
use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexDefinitionNotFoundException;
use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexNotFoundException;
use Adriballa\SymfonySearchBundle\Services\Indexes\Managers\IndexManagerInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Managers\IndexNameManagerInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Repositories\IndexDefinitionRepositoryInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Repositories\IndexMappingRepositoryInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Validation\IndexMappingValidatorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexClient::class)]
class IndexClientTest extends TestCase
{
    private IndexManagerInterface&MockObject $indexManager;
    private IndexNameManagerInterface&MockObject $indexNameManager;
    private IndexDefinitionRepositoryInterface&MockObject $indexDefinitionRepository;
    private IndexMappingRepositoryInterface&MockObject $indexMappingRepository;

    private IndexMappingValidatorInterface&MockObject $indexMappingValidator;
    private IndexClient $client;

    public function setUp(): void
    {
        $this->indexManager = $this->createMock(IndexManagerInterface::class);
        $this->indexNameManager = $this->createMock(IndexNameManagerInterface::class);
        $this->indexDefinitionRepository = $this->createMock(IndexDefinitionRepositoryInterface::class);
        $this->indexMappingRepository = $this->createMock(IndexMappingRepositoryInterface::class);
        $this->indexMappingValidator = $this->createMock(IndexMappingValidatorInterface::class);

        $this->client = new IndexClient(
            $this->indexManager,
            $this->indexNameManager,
            $this->indexDefinitionRepository,
            $this->indexMappingRepository,
            $this->indexMappingValidator,
        );
    }

    public function testIndexExists(): void
    {
        $index = $this->createMock(Index::class);
        $this->indexManager->expects($this->once())
            ->method('indexExists')
            ->willReturn(true)
        ;

        $this->assertTrue($this->client->indexExists($index));
    }

    /**
     * @dataProvider createIndexDataProvider
     */
    public function testCreateIndex(bool $addAlias, bool $deleteExisting): void
    {
        $indexType = 'test_mocks';

        $indexDefinition = $this->createMock(IndexDefinitionInterface::class);
        $indexMapping = $this->createMock(IndexMappingInterface::class);

        $mapping = $this->createMock(IndexMapping::class);
        $index = $this->createMock(Index::class);

        $indexDefinition->expects($this->once())->method('getIndexMapping')->willReturn($indexMapping);

        $this->indexMappingValidator->expects($this->once())
            ->method('validate')
            ->with($indexMapping)
            ->willReturn(true)
        ;

        $this->indexDefinitionRepository->expects($this->once())
            ->method('getIndexDefinition')
            ->with($indexType)
            ->willReturn($indexDefinition)
        ;

        $this->indexMappingRepository->expects($this->once())
            ->method('getIndexMapping')
            ->with($indexType)
            ->willReturn($mapping)
        ;

        $this->indexManager->expects($this->once())
            ->method('createIndex')
            ->with($mapping)
            ->willReturn($index)
        ;

        if ($addAlias) {
            $this->indexManager->expects($this->once())
                ->method('addAliasOnIndex')
                ->with($index, $deleteExisting)
            ;
        }

        $this->assertEquals($index, $this->client->createIndex($indexType, $addAlias, $deleteExisting));
    }

    /**
     * @return mixed[]
     */
    public static function createIndexDataProvider(): array
    {
        return [
            'add alias and delete existing' => [
                'addAlias' => true,
                'deleteExisting' => true,
            ],
            'add alias and no delete existing' => [
                'addAlias' => true,
                'deleteExisting' => false,
            ],
            'no alias no delete existing' => [
                'addAlias' => false,
                'deleteExisting' => false,
            ],
        ];
    }

    /**
     * @dataProvider deleteIndexDataProvider
     */
    public function testDeleteIndex(bool $indexExists, ?string $exception = null): void
    {
        $indexType = 'test_mocks';

        $this->indexDefinitionRepository->expects($this->once())
            ->method('indexDefinitionExists')
            ->with($indexType)
            ->willReturn($indexExists)
        ;

        if (!$indexExists) {
            $this->expectException($exception);
        } else {
            $this->indexManager->expects($this->once())
                ->method('deleteIndex')
                ->with(new Index($indexType))
            ;
        }

        $this->client->deleteIndex($indexType);
    }

    /**
     * @return mixed[]
     */
    public static function deleteIndexDataProvider(): array
    {
        return [
            'index definition found' => [
                'indexExists' => true,
                'exception' => null,
            ],
            'index definition missing' => [
                'indexExists' => false,
                'exception' => IndexDefinitionNotFoundException::class,
            ],
        ];
    }

    /**
     * @dataProvider copyIndexDataProvider
     */
    public function testCopyIndex(
        bool $initialExists,
        bool $addAlias,
        bool $deleteExisting,
        bool $expectException,
        bool $expectCopy,
    ): void {
        $index = new Index('blog');
        $aliasName = 'blog_alias';
        $source = new Index('blog', $aliasName);
        $destination = new Index('blog', 'destination-index');

        $indexDefinition = $this->createMock(IndexDefinitionInterface::class);
        $indexMapping = $this->createMock(IndexMappingInterface::class);

        $this->indexNameManager
            ->expects($this->once())
            ->method('getAliasName')
            ->with('blog')
            ->willReturn($aliasName)
        ;

        $this->indexManager->expects($this->atLeastOnce())
            ->method('indexExists')
            ->with($source)
            ->willReturn($initialExists)
        ;

        if ($expectException) {
            $this->expectException(IndexNotFoundException::class);
        } else {
            $mapping = $this->createMock(IndexMapping::class);
            $indexDefinition->expects($this->once())->method('getIndexMapping')->willReturn($indexMapping);

            $this->indexDefinitionRepository
                ->expects($this->once())
                ->method('getIndexDefinition')
                ->with('blog')
                ->willReturn($indexDefinition)
            ;

            $this->indexMappingValidator->expects($this->once())
                ->method('validate')
                ->with($indexMapping)
                ->willReturn(true)
            ;

            $this->indexMappingRepository
                ->expects($this->once())
                ->method('getIndexMapping')
                ->with('blog')
                ->willReturn($mapping)
            ;

            $this->indexManager
                ->expects($this->once())
                ->method('createIndex')
                ->with($mapping)
                ->willReturn($destination)
            ;
        }

        if ($expectCopy) {
            $this->indexManager->expects($this->once())
                ->method('copyIndex')
                ->with($aliasName, 'destination-index')
            ;

            $this->indexManager->expects($this->once())
                ->method('refreshIndex')
                ->with($destination)
            ;
        } else {
            $this->indexManager->expects($this->never())->method('copyIndex');
            $this->indexManager->expects($this->never())->method('refreshIndex');
        }

        if (!$expectException && $addAlias) {
            $this->indexManager->expects($this->once())
                ->method('addAliasOnIndex')
                ->with($destination, $deleteExisting)
            ;
        } else {
            $this->indexManager->expects($this->never())->method('addAliasOnIndex');
        }

        $result = $this->client->copyIndex($index, $addAlias, $deleteExisting);

        if (!$expectException) {
            $this->assertSame($destination, $result);
        }
    }

    /**
     * @return mixed[]
     */
    public static function copyIndexDataProvider(): array
    {
        return [
            'index does not exists initially - throws' => [
                'initialExists' => false,
                'addAlias' => false,
                'deleteExisting' => false,
                'expectException' => true,
                'expectCopy' => false,
            ],
            'index created, then copy with alias' => [
                'initialExists' => true,
                'addAlias' => true,
                'deleteExisting' => true,
                'expectException' => false,
                'expectCopy' => true,
            ],
            'index never exists - no copy, no alias' => [
                'initialExists' => true,
                'addAlias' => false,
                'deleteExisting' => false,
                'expectException' => false,
                'expectCopy' => true,
            ],
        ];
    }
}
