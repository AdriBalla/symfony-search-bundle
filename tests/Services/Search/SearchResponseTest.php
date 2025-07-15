<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search;

use Adriballa\SymfonySearchBundle\Services\Search\SearchResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SearchResponse::class)]
class SearchResponseTest extends TestCase
{
    /**
     * @dataProvider searchResponseDataProvider
     *
     * @param mixed[] $hits
     * @param mixed[] $aggregations
     */
    public function testSearchResponse(
        string $indexType,
        bool $success,
        int $start,
        int $size,
        int $duration,
        int $totalHits,
        array $hits,
        array $aggregations,
    ): void {
        $response = new SearchResponse($indexType, $success, $start, $size, $duration, $totalHits, $hits, $aggregations);
        $this->assertEquals($indexType, $response->indexType);
        $this->assertEquals($success, $response->success);
        $this->assertEquals($duration, $response->duration);
        $this->assertEquals($totalHits, $response->totalHits);
        $this->assertEquals($hits, $response->hits);
        $this->assertEquals($aggregations, $response->aggregations);
    }

    /**
     * @return mixed[]
     */
    public static function searchResponseDataProvider(): array
    {
        return [
            'all defaults except success' => [
                'indexType' => 'test_index',
                'success' => true,
                'start' => 1,
                'size' => 10,
                'duration' => 0,
                'totalHits' => 0,
                'hits' => [],
                'aggregations' => [],
            ],
            'non-empty hits and aggs' => [
                'indexType' => 'test_index_aggs',
                'success' => false,
                'start' => 0,
                'size' => 50,
                'duration' => 123,
                'totalHits' => 42,
                'hits' => [
                    ['id' => 1],
                    ['id' => 2],
                ],
                'aggregations' => [
                    'category' => ['books' => 10, 'electronics' => 5],
                ],
            ],
            'empty results' => [
                'indexType' => 'test_empty',
                'success' => false,
                'start' => 0,
                'size' => 100,
                'duration' => 0,
                'totalHits' => 0,
                'hits' => [],
                'aggregations' => [],
            ],
            'large values' => [
                'indexType' => 'test_large_volume',
                'success' => true,
                'start' => 100,
                'size' => 210,
                'duration' => 999,
                'totalHits' => 10000,
                'hits' => [
                    ['doc' => 'x'],
                ],
                'aggregations' => ['agg' => ['a' => 999]],
            ],
        ];
    }
}
