<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Transformers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Setting\IndexSettings;
use Adriballa\SymfonySearchBundle\Services\Indexes\Transformers\IndexMappingTransformer;
use Adriballa\SymfonySearchBundle\Services\Indexes\Transformers\IndexSettingsTransformer;
use Adriballa\SymfonySearchBundle\Services\Indexes\Transformers\TextualAnalysisTransformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexMappingTransformer::class)]
class IndexMappingTransformerTest extends TestCase
{
    private IndexSettingsTransformer&MockObject $indexSettingsTransformer;
    private MockObject&TextualAnalysisTransformer $textualAnalysisTransformer;
    private IndexMappingTransformer $indexMappingTransformer;

    public function setUp(): void
    {
        $this->textualAnalysisTransformer = $this->createMock(TextualAnalysisTransformer::class);

        $this->indexSettingsTransformer = $this->createMock(IndexSettingsTransformer::class);

        $this->indexMappingTransformer = new IndexMappingTransformer($this->indexSettingsTransformer, $this->textualAnalysisTransformer);
    }

    public function testGetElasticsearchConfiguration(): void
    {
        $indexMapping = $this->createMock(IndexMappingInterface::class);
        $indexSettings = $this->createMock(IndexSettings::class);

        $field1 = $this->createMock(FieldDefinitionInterface::class);
        $field2 = $this->createMock(FieldDefinitionInterface::class);

        $indexMapping->expects($this->once())
            ->method('getIndexSettings')
            ->willReturn($indexSettings)
        ;

        $expectedSettings = ['settings'];
        $expectedTextualAnalysis = ['textual analysis'];
        $rawMapping = ['raw mapping'];
        $dynamicTemplates = ['dynamic templates'];

        $indexMapping->expects($this->once())
            ->method('getFields')
            ->willReturn([$field1, $field2])
        ;

        $this->indexSettingsTransformer->expects($this->once())
            ->method('transform')
            ->with($indexSettings)
            ->willReturn($expectedSettings)
        ;

        $this->textualAnalysisTransformer->expects($this->once())
            ->method('transform')
            ->with($indexMapping)
            ->willReturn($expectedTextualAnalysis)
        ;

        $indexMapping->expects($this->once())
            ->method('getExplicitMapping')
            ->willReturn($rawMapping)
        ;

        $indexMapping->expects($this->once())
            ->method('getDynamicTemplates')
            ->willReturn($dynamicTemplates)
        ;

        $field1->expects($this->once())
            ->method('getPath')
            ->willReturn('field1')
        ;

        $field2->expects($this->once())
            ->method('getPath')
            ->willReturn('field2')
        ;

        $field1->expects($this->once())
            ->method('getElasticsearchConfiguration')
            ->willReturn(['field 1 configuration'])
        ;

        $field2->expects($this->once())
            ->method('getElasticsearchConfiguration')
            ->willReturn(['field 2 configuration'])
        ;

        $expectedMapping = [
            'mappings' => [
                'raw mapping',
                'properties' => [
                    'field1' => ['field 1 configuration'],
                    'field2' => ['field 2 configuration'],
                ],
                'dynamic_templates' => ['dynamic templates'],
            ],
            'settings' => [
                'settings',
                'textual analysis',
            ],
        ];

        $this->assertEquals($expectedMapping, $this->indexMappingTransformer->getElasticsearchConfiguration($indexMapping));
    }
}
