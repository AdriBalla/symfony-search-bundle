<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\TextualAnalysis;

use Adriballa\SymfonySearchBundle\Services\Indexes\TextualAnalysis\AutocompleteAnalyzer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AutocompleteAnalyzer::class)]
class AutoCompleteAnalyzerTest extends TestCase
{
    private AutocompleteAnalyzer $analyzer;

    protected function setUp(): void
    {
        $this->analyzer = new AutocompleteAnalyzer();
    }

    public function testGetAnalyzerReturnsExpectedArray(): void
    {
        $expected = [
            'tokenizer' => 'autocomplete',
            'filter' => [
                'lowercase',
                'asciifolding',
            ],
        ];

        $this->assertEquals($expected, $this->analyzer->getAnalyzer());
    }

    public function testGetAnalyzerNameReturnsConstant(): void
    {
        $this->assertEquals('autocomplete', $this->analyzer->getAnalyzerName());
    }

    public function testGetFiltersReturnsEmptyArray(): void
    {
        $this->assertEquals([], $this->analyzer->getFilters());
    }

    public function testGetTokenizersReturnsExpectedStructure(): void
    {
        $expected = [
            'autocomplete' => [
                'type' => 'edge_ngram',
                'min_gram' => 2,
                'max_gram' => 20,
                'token_chars' => [
                    'letter',
                ],
            ],
        ];

        $this->assertEquals($expected, $this->analyzer->getTokenizers());
    }

    public function testGetSearchAnalyzerReturnsExpectedArray(): void
    {
        $expected = [
            'tokenizer' => 'lowercase',
        ];

        $this->assertEquals($expected, $this->analyzer->getSearchAnalyzer());
    }

    public function testGetSearchAnalyzerNameReturnsConstant(): void
    {
        $this->assertEquals('autocomplete_search', $this->analyzer->getSearchAnalyzerName());
    }
}
