<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Aggregations;

use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\StatsAggregation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(StatsAggregation::class)]
class StatsAggregationTest extends TestCase
{
    public function testToElasticsearchAggregation(): void
    {
        $aggregation = new StatsAggregation('test');

        $expected = ['test' => ['stats' => ['field' => 'test']]];
        $this->assertEquals($expected, $aggregation->toElasticsearchAggregation());
    }
}
