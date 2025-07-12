<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Fields\Text;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinition;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Text\SearchableTextField;
use Adriballa\SymfonySearchBundle\Services\Indexes\TextualAnalysis\TextualAnalysisInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SearchableTextField::class)]
#[CoversClass(FieldDefinition::class)]
class SearchableTextFieldTest extends TestCase
{
    public function testGetElasticsearchConfigurationWithCustomAnalyzer(): void
    {
        $analyzer = $this->createMock(TextualAnalysisInterface::class);

        $analyzer->expects($this->any())
            ->method('getAnalyzer')
            ->willReturn(['this is an analyzer'])
        ;

        $analyzer->expects($this->any())
            ->method('getAnalyzerName')
            ->willReturn('test_analyzer')
        ;

        $analyzer->expects($this->any())
            ->method('getSearchAnalyzerName')
            ->willReturn('test_search_analyzer')
        ;

        $searchableField = new SearchableTextField(
            path: 'searchable.field',
            autocomplete: true,
            boost: 100,
            textualAnalysis: [
                $analyzer,
            ],
            sortable: true,
        );

        $configuration = [
            'type' => FieldType::SearchableText->value,
            'fields' => [
                'keyword' => [
                    'type' => FieldType::Keyword->value,
                    'index' => false,
                ],
                'autocomplete' => [
                    'type' => FieldType::SearchableText->value,
                    'analyzer' => 'autocomplete',
                    'search_analyzer' => 'autocomplete_search',
                ],
            ],
            'analyzer' => 'test_analyzer',
            'search_analyzer' => 'test_search_analyzer',
        ];

        $esConfig = $searchableField->getElasticsearchConfiguration();

        $this->assertEquals($configuration, $esConfig);
    }

    public function testGetElasticsearchConfigurationWithoutAnalyzer(): void
    {
        $searchableField = new SearchableTextField(
            path: 'searchable.field',
            autocomplete: true,
            boost: 100,
            sortable: true,
        );

        $configuration = [
            'type' => FieldType::SearchableText->value,
            'fields' => [
                'keyword' => [
                    'type' => FieldType::Keyword->value,
                    'index' => false,
                ],
                'autocomplete' => [
                    'type' => FieldType::SearchableText->value,
                    'analyzer' => 'autocomplete',
                    'search_analyzer' => 'autocomplete_search',
                ],
            ],
            'analyzer' => 'simple_analyzer',
            'search_analyzer' => 'simple_analyzer',
        ];

        $esConfig = $searchableField->getElasticsearchConfiguration();

        $this->assertEquals($configuration, $esConfig);
    }
}
