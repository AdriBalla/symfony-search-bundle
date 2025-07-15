<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Factories;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Search\Factories\SearchResponseFactory;
use Adriballa\SymfonySearchBundle\Services\Search\PendingSearchRequest;
use Adriballa\SymfonySearchBundle\Services\Search\Range;
use Adriballa\SymfonySearchBundle\Services\Search\SearchResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SearchResponseFactory::class)]
class SearchResponseFactoryTest extends TestCase
{
    private SearchResponseFactory $searchResponseFactory;

    public function setUp(): void
    {
        $this->searchResponseFactory = new SearchResponseFactory();
    }

    /**
     * @dataProvider searchResponseDataProvider
     *
     * @param mixed[] $input
     * @param mixed[] $expected
     */
    public function testCreateFromResult(Index $index, Range $range, array $input, array $expected): void
    {
        $searchRequest = $this->createMock(PendingSearchRequest::class);
        $searchRequest->method('getRange')->willReturn($range);
        $searchRequest->method('getIndex')->willReturn($index);

        $response = $this->searchResponseFactory->createFromResult($input, $searchRequest);

        $this->assertInstanceOf(SearchResponse::class, $response);
        $this->assertTrue($response->success);
        $this->assertSame($range->getStart(), $response->start);
        $this->assertSame($range->getSize(), $response->size);
        $this->assertSame($index->getType(), $response->indexType);
        $this->assertSame($expected['took'], $response->duration);
        $this->assertSame($expected['total'], $response->totalHits);
        $this->assertEquals($expected['documents'], $response->hits);
        $this->assertEquals($expected['aggregations'], $response->aggregations);
    }

    /**
     * @return mixed[]
     */
    public static function searchResponseDataProvider(): array
    {
        return [
            'with_aggregations' => [
                'index' => new Index('test_index'),
                'range' => new Range(0, 10),
                'input' => [
                    'took' => 123,
                    'hits' => [
                        'total' => ['value' => 5],
                        'hits' => [
                            ['_source' => ['id' => 1, 'name' => 'Item 1']],
                            ['_source' => ['id' => 2, 'name' => 'Item 2']],
                        ],
                    ],
                    'aggregations' => [
                        'category.keyword' => [
                            'buckets' => [
                                ['key' => 'Books', 'doc_count' => 3],
                                ['key' => 'Tech', 'doc_count' => 2],
                            ],
                        ],
                        'price_stats' => [
                            'value_count' => ['value' => 2],
                            'price_ranges' => [
                                'buckets' => [
                                    ['key' => '0-10', 'doc_count' => 1],
                                    ['key' => '10-100', 'doc_count' => 1],
                                ],
                            ],
                        ],
                    ],
                ],
                'expected' => [
                    'took' => 123,
                    'total' => 5,
                    'documents' => [
                        ['id' => 1, 'name' => 'Item 1'],
                        ['id' => 2, 'name' => 'Item 2'],
                    ],
                    'aggregations' => [
                        'category' => [
                            'Books' => 3,
                            'Tech' => 2,
                        ],
                        'price_ranges' => [
                            '0-10' => 1,
                            '10-100' => 1,
                        ],
                    ],
                ],
            ],
            'without_aggregations' => [
                'index' => new Index('test_index'),
                'range' => new Range(0, 10),
                'input' => [
                    'took' => 50,
                    'hits' => [
                        'total' => ['value' => 0],
                        'hits' => [],
                    ],
                    // no aggregations key
                ],
                'expected' => [
                    'took' => 50,
                    'total' => 0,
                    'documents' => [],
                    'aggregations' => [],
                ],
            ],
        ];
    }
}
