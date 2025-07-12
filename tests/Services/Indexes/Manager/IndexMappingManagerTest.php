<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Manager;

use Adriballa\SymfonySearchBundle\Services\Indexes\Definition\IndexDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\FieldInfo;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\BooleanField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\FloatField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\KeywordField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\SearchOptions;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Text\SearchableKeywordField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Text\SearchableTextField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Managers\IndexMappingManager;
use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Repositories\IndexDefinitionRepositoryInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Resolvers\IndexMappingFieldsResolver;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScopeService;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScopeServiceInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexMappingManager::class)]
class IndexMappingManagerTest extends TestCase
{
    private IndexDefinitionRepositoryInterface&MockObject $indexDefinitionRepository;
    private IndexMappingFieldsResolver&MockObject $indexMappingFieldsResolver;
    private FieldScopeServiceInterface $fieldScopeService;

    private IndexMappingManager $indexMappingManager;

    public function setUp(): void
    {
        $this->indexMappingFieldsResolver = $this->createMock(IndexMappingFieldsResolver::class);
        $this->indexDefinitionRepository = $this->createMock(IndexDefinitionRepositoryInterface::class);
        $this->fieldScopeService = new FieldScopeService();

        $this->indexMappingManager = new IndexMappingManager(
            $this->indexDefinitionRepository,
            $this->indexMappingFieldsResolver,
            $this->fieldScopeService,
        );
    }

    public function testGetFilterableFields(): void
    {
        $indexType = 'test_mock';
        $index = $this->createMock(Index::class);
        $indexDefinition = $this->createMock(IndexDefinitionInterface::class);
        $indexMapping = $this->createMock(IndexMappingInterface::class);

        $index->expects($this->once())->method('getType')->willReturn($indexType);
        $indexDefinition->expects($this->once())->method('getIndexMapping')->willReturn($indexMapping);

        $fieldDefinitions = [
            'name' => new SearchableKeywordField('name'),
            'description' => new SearchableTextField('name'),
            'reference' => new KeywordField('reference'),
            'age' => new FloatField('age', true),
            'price' => new FloatField('age', true, false),
            'active' => new BooleanField('active', FieldScope::Public, true),
            'secret_locked' => new BooleanField('secret_locked'),
            'secret' => new KeywordField('secret', new SearchOptions(1), FieldScope::Private),
        ];

        $expected = [
            new FieldInfo('name', FieldType::Keyword),
            new FieldInfo('description', FieldType::SearchableText),
            new FieldInfo('age', FieldType::Float),
            new FieldInfo('active', FieldType::Boolean),
        ];

        $this->indexDefinitionRepository->expects($this->once())
            ->method('getIndexDefinition')
            ->with($indexType)
            ->willReturn($indexDefinition)
        ;

        $this->indexMappingFieldsResolver->expects($this->once())
            ->method('resolve')
            ->with($indexMapping)
            ->willReturn($fieldDefinitions)
        ;

        $this->assertEquals($expected, $this->indexMappingManager->getFilterableFields($index));
    }

    public function testGetSortableFields(): void
    {
        $indexType = 'test_mock';
        $index = $this->createMock(Index::class);
        $indexDefinition = $this->createMock(IndexDefinitionInterface::class);
        $indexMapping = $this->createMock(IndexMappingInterface::class);

        $index->expects($this->once())->method('getType')->willReturn($indexType);
        $indexDefinition->expects($this->once())->method('getIndexMapping')->willReturn($indexMapping);

        $fieldDefinitions = [
            'name' => new SearchableKeywordField('name', true),
            'description' => new SearchableTextField('name'),
            'reference' => new KeywordField('reference', null, FieldScope::Public, true),
            'age' => new FloatField('age', true),
            'active' => new BooleanField('active', FieldScope::Public, false, true),
            'secret' => new KeywordField('secret', new SearchOptions(1), FieldScope::Private),
        ];

        $expected = [
            new FieldInfo('name', FieldType::Keyword),
            new FieldInfo('reference', FieldType::Keyword),
            new FieldInfo('age', FieldType::Float),
            new FieldInfo('active', FieldType::Boolean),
        ];

        $this->indexDefinitionRepository->expects($this->once())
            ->method('getIndexDefinition')
            ->with($indexType)
            ->willReturn($indexDefinition)
        ;

        $this->indexMappingFieldsResolver->expects($this->once())
            ->method('resolve')
            ->with($indexMapping)
            ->willReturn($fieldDefinitions)
        ;

        $this->assertEquals($expected, $this->indexMappingManager->getSortableFields($index));
    }
}
