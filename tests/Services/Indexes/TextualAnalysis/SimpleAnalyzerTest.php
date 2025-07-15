<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\TextualAnalysis;

use Adriballa\SymfonySearchBundle\Services\Indexes\TextualAnalysis\SimpleAnalyzer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SimpleAnalyzer::class)]
class SimpleAnalyzerTest extends TestCase
{
    private SimpleAnalyzer $analyzer;

    protected function setUp(): void
    {
        $this->analyzer = new SimpleAnalyzer();
    }

    public function testGetAnalyzerReturnsExpectedArray(): void
    {
        $expected = [
            'type' => 'custom',
            'tokenizer' => 'lowercase',
            'filter' => [
                'classic',
                'asciifolding',
                'unique',
            ],
        ];

        $this->assertEquals($expected, $this->analyzer->getAnalyzer());
    }

    public function testGetAnalyzerNameReturnsConstant(): void
    {
        $this->assertEquals('simple_analyzer', $this->analyzer->getAnalyzerName());
    }

    public function testGetFiltersReturnsEmptyArray(): void
    {
        $this->assertEquals([], $this->analyzer->getFilters());
    }

    public function testGetTokenizersReturnsEmptyArray(): void
    {
        $this->assertEquals([], $this->analyzer->getTokenizers());
    }

    public function testGetSearchAnalyzerReturnsNull(): void
    {
        $this->assertNull($this->analyzer->getSearchAnalyzer());
    }

    public function testGetSearchAnalyzerNameReturnsNull(): void
    {
        $this->assertNull($this->analyzer->getSearchAnalyzerName());
    }
}
