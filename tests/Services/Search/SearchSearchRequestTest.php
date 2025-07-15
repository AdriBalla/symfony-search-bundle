<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\AggregationInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Enums\SortDirection;
use Adriballa\SymfonySearchBundle\Services\Search\Factories\QueryFactoryInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\FilterableInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Range;
use Adriballa\SymfonySearchBundle\Services\Search\SearchRequest;
use Adriballa\SymfonySearchBundle\Services\Search\Sort\Sort;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SearchRequest::class)]
class SearchSearchRequestTest extends TestCase
{
    public function testSearchRequestAccessors(): void
    {
        $index = new Index('test_index');
        $queryString = 'test query';
        $range = new Range(10, 25);
        $fieldsToFetch = ['title', 'description'];
        $fieldToSearch = ['description'];

        $aggregation = $this->createMock(AggregationInterface::class);
        $filter = $this->createMock(FilterableInterface::class);
        $sort = new Sort('title', SortDirection::ASC);
        $queryFactory = $this->createMock(QueryFactoryInterface::class);

        $searchRequest = new SearchRequest(
            $index,
            $queryString,
            $range,
            $fieldToSearch,
            $fieldsToFetch,
            [$aggregation],
            [$filter],
            [$sort],
            $queryFactory,
        );

        $this->assertEquals($index, $searchRequest->getIndex());
        $this->assertEquals($queryString, $searchRequest->getQueryString());
        $this->assertEquals($range, $searchRequest->getRange());
        $this->assertEquals($fieldsToFetch, $searchRequest->getFieldsToFetch());
        $this->assertEquals([$aggregation], $searchRequest->getAggregations());
        $this->assertEquals([$filter], $searchRequest->getFilters());
        $this->assertEquals([$sort], $searchRequest->getSorts());
        $this->assertEquals($queryFactory, $searchRequest->getQueryFactory());
        $this->assertEquals($fieldToSearch, $searchRequest->getFieldsToSearch());
    }

    public function testSearchRequestWithNullValues(): void
    {
        $index = new Index('empty_index');

        $searchRequest = new SearchRequest(
            $index,
            null,
            null,
            [],
            [],
            [],
            [],
            [],
            null,
        );

        $this->assertEquals($index, $searchRequest->getIndex());
        $this->assertNull($searchRequest->getQueryString());
        $this->assertNull($searchRequest->getRange());
        $this->assertEquals([], $searchRequest->getFieldsToFetch());
        $this->assertEquals([], $searchRequest->getAggregations());
        $this->assertEquals([], $searchRequest->getFilters());
        $this->assertEquals([], $searchRequest->getSorts());
        $this->assertNull($searchRequest->getQueryFactory());
        $this->assertEquals([], $searchRequest->getFieldsToSearch());
    }
}
