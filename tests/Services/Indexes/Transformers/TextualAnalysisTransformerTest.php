<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Transformers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Resolvers\IndexMappingFieldsResolver;
use Adriballa\SymfonySearchBundle\Services\Indexes\TextualAnalysis\TextualAnalysisInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Transformers\TextualAnalysisTransformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(TextualAnalysisTransformer::class)]
class TextualAnalysisTransformerTest extends TestCase
{
    private IndexMappingFieldsResolver&MockObject $indexMappingFieldsResolver;

    private TextualAnalysisTransformer $textualAnalysisTransformer;

    public function setUp(): void
    {
        $this->indexMappingFieldsResolver = $this->createMock(IndexMappingFieldsResolver::class);

        $this->textualAnalysisTransformer = new TextualAnalysisTransformer($this->indexMappingFieldsResolver);
    }

    /**
     * @dataProvider textualAnalysisDataProvider
     *
     * @param mixed[] $textualAnalysesParameters
     * @param mixed[] $expected
     */
    public function testTransform(
        array $textualAnalysesParameters,
        array $expected,
    ): void {
        $fields = [];

        $textualAnalysis = $this->mockTextualAnalysis($textualAnalysesParameters['filters'], $textualAnalysesParameters['tokenizers'], $textualAnalysesParameters['analyzer'], $textualAnalysesParameters['analyzerName'], $textualAnalysesParameters['searchAnalyzer'], $textualAnalysesParameters['searchAnalyzerName']);
        $field = $this->createMock(FieldDefinitionInterface::class);
        $field->method('getTextualAnalysis')->willReturn([$textualAnalysis]);
        $fields[] = $field;

        $indexMapping = $this->createMock(IndexMappingInterface::class);

        $this->indexMappingFieldsResolver->method('resolve')->with($indexMapping)->willReturn($fields);

        $result = $this->textualAnalysisTransformer->transform($indexMapping);

        $this->assertSame($expected, $result);
    }

    /**
     * @return mixed[]
     */
    public static function textualAnalysisDataProvider(): array
    {
        return [
            'full analysis' => [
                'textualAnalysesParameters' => [
                    'filters' => ['my_filter' => ['type' => 'lowercase']],
                    'tokenizers' => ['my_tokenizer' => ['type' => 'edge_ngram']],
                    'analyzer' => ['type' => 'custom'],
                    'analyzerName' => 'custom_analyzer',
                    'searchAnalyzer' => ['type' => 'standard'],
                    'searchAnalyzerName' => 'search_analyzer',
                ],
                'expected' => [
                    'analysis' => [
                        'filter' => ['my_filter' => ['type' => 'lowercase']],
                        'tokenizer' => ['my_tokenizer' => ['type' => 'edge_ngram']],
                        'analyzer' => [
                            'custom_analyzer' => ['type' => 'custom'],
                            'search_analyzer' => ['type' => 'standard'],
                        ],
                    ],
                ],
            ],
            'no analysis' => [
                'textualAnalysesParameters' => [
                    'filters' => null,
                    'tokenizers' => null,
                    'analyzer' => null,
                    'analyzerName' => null,
                    'searchAnalyzer' => null,
                    'searchAnalyzerName' => null,
                ],
                'expected' => [
                    'analysis' => [
                        'filter' => [],
                        'tokenizer' => [],
                        'analyzer' => [],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param null|mixed[] $filters
     * @param null|mixed[] $tokenizers
     * @param null|mixed[] $analyzer
     * @param null|mixed[] $searchAnalyzer
     */
    private function mockTextualAnalysis(
        ?array $filters = null,
        ?array $tokenizers = null,
        ?array $analyzer = null,
        ?string $analyzerName = null,
        ?array $searchAnalyzer = null,
        ?string $searchAnalyzerName = null,
    ): TextualAnalysisInterface {
        $mock = $this->createMock(TextualAnalysisInterface::class);

        $mock->method('getFilters')->willReturn($filters);
        $mock->method('getTokenizers')->willReturn($tokenizers);
        $mock->method('getAnalyzer')->willReturn($analyzer);
        $mock->method('getAnalyzerName')->willReturn($analyzerName);
        $mock->method('getSearchAnalyzer')->willReturn($searchAnalyzer);
        $mock->method('getSearchAnalyzerName')->willReturn($searchAnalyzerName);

        return $mock;
    }
}
