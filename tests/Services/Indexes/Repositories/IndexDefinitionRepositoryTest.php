<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Repositories;

use Adriballa\SymfonySearchBundle\Services\Indexes\Definition\IndexDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexDefinitionNotFoundException;
use Adriballa\SymfonySearchBundle\Services\Indexes\Repositories\IndexDefinitionRepository;
use Adriballa\SymfonySearchBundle\Tests\Stubs\TestIndexDefinition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexDefinitionRepository::class)]
class IndexDefinitionRepositoryTest extends TestCase
{
    private IndexDefinitionRepository $repository;

    private IndexDefinitionInterface $indexDefinition;

    protected function setUp(): void
    {
        parent::setUp();

        $this->indexDefinition = new TestIndexDefinition();

        $this->repository = new IndexDefinitionRepository(
            [$this->indexDefinition],
        );
    }

    /**
     * @dataProvider indexDefinitionDataProvider
     */
    public function testGetIndexDefinition(string $indexType, ?string $exception): void
    {
        if ($exception) {
            $this->expectException($exception);
        }

        $indexDefinition = $this->repository->getIndexDefinition($indexType);

        if (!$exception) {
            $this->assertEquals($this->indexDefinition, $indexDefinition);
        }
    }

    /**
     * @return mixed[]
     */
    public static function indexDefinitionDataProvider(): array
    {
        return [
            'index definition exists' => [
                'indexType' => TestIndexDefinition::getIndexType(),
                'exception' => null,
            ],
            'index definition missing' => [
                'indexType' => 'wrong_index_definition',
                'exception' => IndexDefinitionNotFoundException::class,
            ],
        ];
    }

    public function testIndexDefinitionExists(): void
    {
        $this->assertTrue($this->repository->indexDefinitionExists('test_mocks'));
        $this->assertFalse($this->repository->indexDefinitionExists('wrong_index_definition'));
    }
}
