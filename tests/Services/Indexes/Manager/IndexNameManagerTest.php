<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Manager;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Managers\IndexNameManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexNameManager::class)]
class IndexNameManagerTest extends TestCase
{
    private string $indexSeparator = '-';

    private string $indexPrefix = 'test-';

    private IndexNameManager $indexNameManager;

    public function setUp(): void
    {
        $this->indexNameManager = new IndexNameManager($this->indexSeparator, $this->indexPrefix);
    }

    public function testGetIndexSeparator(): void
    {
        $this->assertEquals($this->indexSeparator, $this->indexNameManager->getIndexSeparator());
    }

    public function testGetIndexPrefix(): void
    {
        $this->assertEquals($this->indexPrefix, $this->indexNameManager->getIndexPrefix());
    }

    public function testGetIndexSuffix(): void
    {
        $this->assertNotEmpty($this->indexNameManager->getIndexSuffix());
    }

    public function testGetAliasName(): void
    {
        $indexType = 'mocks';
        $expectedAlias = 'test-mocks';

        $this->assertEquals($expectedAlias, $this->indexNameManager->getAliasName($indexType));
    }

    public function testGetIndexNameForType(): void
    {
        $indexType = 'mocks';
        $suffix = '20250101';

        $expectedName = sprintf('%s%s-%s', $this->indexPrefix, $indexType, $suffix);

        $this->assertEquals($expectedName, $this->indexNameManager->getIndexNameForType($indexType, $suffix));
    }

    /**
     * @dataProvider getIndexNameDataProvider
     */
    public function testGetIndexName(Index $index, string $expected): void
    {
        $this->assertEquals($expected, $this->indexNameManager->getIndexName($index));
    }

    /**
     * @return mixed[]
     */
    public static function getIndexNameDataProvider(): array
    {
        return [
            'index with name' => [
                'index' => new Index('test_type', 'test_name'),
                'expected' => 'test_name',
            ],
            'index with only type' => [
                'index' => new Index('test_type'),
                'expected' => 'test-test_type',
            ],
        ];
    }
}
