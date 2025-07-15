<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Filters;

use Adriballa\SymfonySearchBundle\Services\Search\Filters\ScriptedFilter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ScriptedFilter::class)]
class ScriptedFilterTest extends TestCase
{
    private ScriptedFilter $scriptedFilter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->scriptedFilter = new ScriptedFilter('script', ['value' => 'test']);
    }

    public function testGetScript(): void
    {
        $this->assertEquals('script', $this->scriptedFilter->getScript());
    }

    public function testGetParams(): void
    {
        $this->assertEquals(['value' => 'test'], $this->scriptedFilter->getParams());
    }

    public function testToElasticsearchFilter(): void
    {
        $expected = [
            'script' => [
                'script' => [
                    'source' => 'script',
                    'lang' => 'painless',
                    'params' => ['value' => 'test'],
                ],
            ],
        ];

        $this->assertEquals($expected, $this->scriptedFilter->toElasticsearchFilter());
    }
}
