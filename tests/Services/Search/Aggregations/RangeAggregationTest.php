<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Aggregations;

use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\AggregationRange;
use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\RangeAggregation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RangeAggregation::class)]
class RangeAggregationTest extends TestCase
{
    /**
     * @dataProvider rangeAggregationDataProvider
     *
     * @param AggregationRange[] $ranges
     * @param mixed[]            $expected
     */
    public function testToElasticsearchAggregation(array $ranges, array $expected): void
    {
        $rangeAggregation = new RangeAggregation('test', $ranges);
        $this->assertEquals($expected, $rangeAggregation->toElasticsearchAggregation());
    }

    /**
     * @return mixed[]
     */
    public static function rangeAggregationDataProvider(): array
    {
        return [
            'with ranges' => [
                'ranges' => [
                    new AggregationRange('below 10', 0, 10),
                    new AggregationRange('between 10 and 20', 11, 20),
                    new AggregationRange('above 20', 21, null),
                ],
                'expected' => [
                    'test' => [
                        'range' => [
                            'field' => 'test',
                            'ranges' => [
                                ['key' => 'below 10', 'to' => 10],
                                ['key' => 'between 10 and 20', 'from' => 11, 'to' => 20],
                                ['key' => 'above 20', 'from' => 21],
                            ],
                        ],
                    ],
                ],
            ],
            'without ranges' => [
                'ranges' => [],
                'expected' => [
                    'test' => [
                        'range' => [
                            'field' => 'test',
                            'ranges' => [
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
