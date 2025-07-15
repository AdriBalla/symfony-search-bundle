<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Aggregations;

use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\AggregationHighlight;
use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\TermAggregation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TermAggregation::class)]
class TermAggregationTest extends TestCase
{
    public function testFiltersAccessors(): void
    {
        $aggregation = new TermAggregation('test');
        $this->assertEmpty($aggregation->getFilters());

        $filters = ['custom_filter', 'random_filter'];
        $aggregation->setFilters($filters);
        $this->assertEquals($filters, $aggregation->getFilters());
    }

    /**
     * @dataProvider termAggregationsDataProvider
     *
     * @param null|string[] $filters
     * @param mixed[]       $expectedAggregations
     */
    public function testToElasticSearchAggregations(?array $filters, string $field, array $expectedAggregations): void
    {
        $termAggregation = new TermAggregation(
            $field,
            TermAggregation::MAX_TERMS_AGGREGATION_SIZE,
            10,
            new AggregationHighlight(),
            $filters,
        );

        $this->assertEquals($expectedAggregations, $termAggregation->toElasticsearchAggregation());
    }

    /**
     * @return mixed[]
     */
    public static function termAggregationsDataProvider(): array
    {
        return [
            'term aggregation with prefix' => [
                'filters' => ['test', 'another_test'],
                'field' => 'mock',
                'expectedAggregations' => [
                    'mock__filter' => [
                        'filter' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'prefix' => [
                                            'mock' => [
                                                'value' => 'test',
                                                'case_insensitive' => true,
                                            ],
                                        ],
                                    ],
                                    [
                                        'prefix' => [
                                            'mock' => [
                                                'value' => 'another_test',
                                                'case_insensitive' => true,
                                            ],
                                        ],
                                    ],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                        'aggs' => [
                            'mock' => [
                                'terms' => [
                                    'field' => 'mock',
                                    'size' => 5000,
                                    'min_doc_count' => 10,
                                ],
                                'aggs' => [
                                    'top_hit' => [
                                        'top_hits' => [
                                            '_source' => 'mock',
                                            'size' => 1,
                                            'highlight' => [
                                                'fields' => [
                                                    'mock' => [
                                                        'pre_tags' => [
                                                            '<em>',
                                                        ],
                                                        'post_tags' => [
                                                            '</em>',
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'term aggregation without filters' => [
                'filters' => null,
                'field' => 'another_mock',
                'expectedAggregations' => [
                    'another_mock' => [
                        'terms' => [
                            'field' => 'another_mock',
                            'size' => 5000,
                            'min_doc_count' => 10,
                        ],
                        'aggs' => [
                            'top_hit' => [
                                'top_hits' => [
                                    '_source' => 'another_mock',
                                    'size' => 1,
                                    'highlight' => [
                                        'fields' => [
                                            'another_mock' => [
                                                'pre_tags' => [
                                                    '<em>',
                                                ],
                                                'post_tags' => [
                                                    '</em>',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
