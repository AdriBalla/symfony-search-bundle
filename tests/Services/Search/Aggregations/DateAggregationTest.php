<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Aggregations;

use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\AggregationRange;
use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\DateAggregation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DateAggregation::class)]
class DateAggregationTest extends TestCase
{
    /**
     * @dataProvider dateRangeAggregationProvider
     *
     * @param AggregationRange[]   $ranges
     * @param array<string, mixed> $expectedResult
     */
    public function testToElasticsearchAggregation(?array $ranges, string $format, array $expectedResult): void
    {
        $dateRangeAggregation = new DateAggregation('test_date_aggregation', $ranges, $format);

        $this->assertEquals($expectedResult, $dateRangeAggregation->toElasticsearchAggregation());
    }

    /**
     * @return mixed[]
     */
    public static function dateRangeAggregationProvider(): array
    {
        return [
            'Test with specific range' => [
                'ranges' => [new AggregationRange('test_range', '2022-01-01', '2022-12-31')],
                'format' => 'yyyy-MM-dd',
                'expectedResult' => [
                    'test_date_aggregation' => [
                        'date_range' => [
                            'field' => 'test_date_aggregation',
                            'ranges' => [
                                ['key' => 'test_range', 'from' => '2022-01-01', 'to' => '2022-12-31'],
                            ],
                            'format' => 'yyyy-MM-dd',
                            'missing' => DateAggregation::MISSING_VALUE,
                        ],
                    ],
                ],
            ],
            'Test with multiple ranges' => [
                'ranges' => [
                    new AggregationRange('range1', '2022-01-01', null),
                    new AggregationRange('range2', null, '2022-12-31'),
                ],
                'format' => 'yyyy-MM-dd',
                'expectedResult' => [
                    'test_date_aggregation' => [
                        'date_range' => [
                            'field' => 'test_date_aggregation',
                            'ranges' => [
                                ['key' => 'range1', 'from' => '2022-01-01'],
                                ['key' => 'range2', 'to' => '2022-12-31'],
                            ],
                            'format' => 'yyyy-MM-dd',
                            'missing' => DateAggregation::MISSING_VALUE,
                        ],
                    ],
                ],
            ],
            'Test with different format' => [
                'ranges' => [new AggregationRange('test_range', '2022-01', '2022-12')],
                'format' => 'yyyy-MM',
                'expectedResult' => [
                    'test_date_aggregation' => [
                        'date_range' => [
                            'field' => 'test_date_aggregation',
                            'ranges' => [
                                ['key' => 'test_range', 'from' => '2022-01', 'to' => '2022-12'],
                            ],
                            'format' => 'yyyy-MM',
                            'missing' => DateAggregation::MISSING_VALUE,
                        ],
                    ],
                ],
            ],
            'Test with no ranges' => [
                'ranges' => null,
                'format' => 'yyyy-MM',
                'expectedResult' => [
                    'test_date_aggregation' => [
                        'date_range' => [
                            'field' => 'test_date_aggregation',
                            'ranges' => [],
                            'format' => 'yyyy-MM',
                            'missing' => DateAggregation::MISSING_VALUE,
                        ],
                    ],
                ],
            ],
        ];
    }
}
