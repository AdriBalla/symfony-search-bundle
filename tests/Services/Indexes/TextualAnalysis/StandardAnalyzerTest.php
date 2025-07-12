<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\TextualAnalysis;

use Adriballa\SymfonySearchBundle\Services\Indexes\TextualAnalysis\StandardAnalyzer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(StandardAnalyzer::class)]
class StandardAnalyzerTest extends TestCase
{
    private StandardAnalyzer $analyzer;

    protected function setUp(): void
    {
        $this->analyzer = new StandardAnalyzer();
    }

    public function testGetAnalyzerReturnsExpectedArray(): void
    {
        $expected = [
            'type' => 'standard',
            'tokenizer' => 'standard',
            'filter' => [
                'lowercase',
            ],
        ];

        $this->assertEquals($expected, $this->analyzer->getAnalyzer());
    }

    public function testGetAnalyzerNameReturnsConstant(): void
    {
        $this->assertEquals('standard_analyzer', $this->analyzer->getAnalyzerName());
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
