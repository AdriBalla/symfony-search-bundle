<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Factories;

use Adriballa\SymfonySearchBundle\Services\Search\Factories\ElasticsearchAggregationFactory;
use Adriballa\SymfonySearchBundle\Services\Search\Factories\ElasticsearchFilterFactory;
use Adriballa\SymfonySearchBundle\Services\Search\Factories\ElasticsearchRequestFactory;
use Adriballa\SymfonySearchBundle\Services\Search\Factories\ElasticsearchSortFactory;
use Adriballa\SymfonySearchBundle\Services\Search\Factories\QueryFactoryInterface;
use Adriballa\SymfonySearchBundle\Services\Search\PendingSearchRequest;
use Adriballa\SymfonySearchBundle\Services\Search\Range;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ElasticsearchRequestFactory::class)]
class ElasticsearchRequestFactoryTest extends TestCase
{
    private ElasticsearchAggregationFactory&MockObject $aggregationFactory;
    private ElasticsearchFilterFactory&MockObject $filterFactory;
    private ElasticsearchSortFactory&MockObject $sortFactory;
    private ElasticsearchRequestFactory $elasticsearchRequestFactory;

    public function setUp(): void
    {
        $this->aggregationFactory = $this->createMock(ElasticsearchAggregationFactory::class);
        $this->filterFactory = $this->createMock(ElasticsearchFilterFactory::class);
        $this->sortFactory = $this->createMock(ElasticsearchSortFactory::class);

        $this->elasticsearchRequestFactory = new ElasticsearchRequestFactory(
            $this->aggregationFactory,
            $this->filterFactory,
            $this->sortFactory,
        );
    }

    public function testGetElasticsearchQueryBuildsCorrectQuery(): void
    {
        $queryString = 'test query';
        $filters = ['term' => ['status' => 'active']];
        $aggregations = ['brand' => ['terms' => ['field' => 'brand.keyword']]];
        $sorts = [['price' => 'asc']];
        $fieldsToFetch = ['title', 'price'];

        $range = $this->createMock(Range::class);
        $range->expects($this->once())->method('getStart')->willReturn(0);
        $range->expects($this->once())->method('getSize')->willReturn(10);

        $textQuery = [
            'multi_match' => [
                'query' => $queryString,
                'fields' => ['title'],
            ],
        ];

        $queryFactory = $this->createMock(QueryFactoryInterface::class);
        $queryFactory->expects($this->once())->method('getQueryFromRequest')->willReturn($textQuery);

        $this->filterFactory->expects($this->once())->method('generateFilter')->with($filters)->willReturn($filters);
        $this->aggregationFactory->expects($this->once())->method('generateAggregations')->with($aggregations)->willReturn($aggregations);
        $this->sortFactory->expects($this->once())->method('generateSort')->with($sorts)->willReturn($sorts);

        $pendingSearchRequest = $this->createMock(PendingSearchRequest::class);
        $pendingSearchRequest->method('getQueryString')->willReturn($queryString);
        $pendingSearchRequest->method('getQueryFactory')->willReturn($queryFactory);
        $pendingSearchRequest->method('getFilters')->willReturn($filters);
        $pendingSearchRequest->method('getAggregations')->willReturn($aggregations);
        $pendingSearchRequest->method('getSorts')->willReturn($sorts);
        $pendingSearchRequest->method('getFieldsToFetch')->willReturn($fieldsToFetch);
        $pendingSearchRequest->method('getRange')->willReturn($range);

        $result = $this->elasticsearchRequestFactory->getElasticsearchQuery($pendingSearchRequest);

        $this->assertSame(0, $result['from']);
        $this->assertSame(10, $result['size']);
        $this->assertSame($sorts, $result['sort']);
        $this->assertSame($aggregations, $result['aggs']);
        $this->assertSame(['includes' => $fieldsToFetch], $result['_source']);
        $this->assertArrayHasKey('query', $result);
        $this->assertArrayHasKey('bool', $result['query']);
        $this->assertSame($filters, $result['query']['bool']['filter']);
        $this->assertSame($textQuery, $result['query']['bool']['must']);
    }

    public function testFallbackToMatchAllWhenNoQueryAndNoFilter(): void
    {
        $range = $this->createMock(Range::class);
        $range->method('getStart')->willReturn(5);
        $range->method('getSize')->willReturn(25);

        $queryFactory = $this->createMock(QueryFactoryInterface::class);

        $pendingSearchRequest = $this->createMock(PendingSearchRequest::class);
        $pendingSearchRequest->method('getQueryString')->willReturn('');
        $pendingSearchRequest->method('getQueryFactory')->willReturn($queryFactory);
        $pendingSearchRequest->method('getFilters')->willReturn([]);
        $pendingSearchRequest->method('getAggregations')->willReturn([]);
        $pendingSearchRequest->method('getSorts')->willReturn([]);
        $pendingSearchRequest->method('getFieldsToFetch')->willReturn([]);
        $pendingSearchRequest->method('getRange')->willReturn($range);

        $result = $this->elasticsearchRequestFactory->getElasticsearchQuery($pendingSearchRequest);

        $this->assertEquals(['match_all' => new \stdClass()], $result['query']['bool']['filter']);
        $this->assertEquals(5, $result['from']);
        $this->assertEquals(25, $result['size']);
    }
}
