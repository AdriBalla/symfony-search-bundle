<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\AggregationInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Enums\SortDirection;
use Adriballa\SymfonySearchBundle\Services\Search\Factories\QueryFactoryInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\FilterableInterface;
use Adriballa\SymfonySearchBundle\Services\Search\PendingSearchRequest;
use Adriballa\SymfonySearchBundle\Services\Search\Range;
use Adriballa\SymfonySearchBundle\Services\Search\SearchField;
use Adriballa\SymfonySearchBundle\Services\Search\Sort\Sort;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PendingSearchRequest::class)]
class PendingSearchRequestTest extends TestCase
{
    public function testPendingSearchRequestAccessors(): void
    {
        $index = new Index('test_index');
        $queryString = 'test query';
        $range = new Range(10, 25);
        $fieldsToFetch = ['title', 'description'];

        $aggregation = $this->createMock(AggregationInterface::class);
        $filter = $this->createMock(FilterableInterface::class);
        $sort = new Sort('title', SortDirection::ASC);
        $queryFactory = $this->createMock(QueryFactoryInterface::class);
        $searchField = new SearchField('title', 1);

        $pendingRequest = new PendingSearchRequest(
            $index,
            $queryString,
            $range,
            $fieldsToFetch,
            [$aggregation],
            [$filter],
            [$sort],
            $queryFactory,
            [$searchField],
        );

        $this->assertSame($index, $pendingRequest->getIndex());
        $this->assertSame($queryString, $pendingRequest->getQueryString());
        $this->assertSame($range, $pendingRequest->getRange());
        $this->assertSame($fieldsToFetch, $pendingRequest->getFieldsToFetch());
        $this->assertSame([$aggregation], $pendingRequest->getAggregations());
        $this->assertSame([$filter], $pendingRequest->getFilters());
        $this->assertSame([$sort], $pendingRequest->getSorts());
        $this->assertSame($queryFactory, $pendingRequest->getQueryFactory());
        $this->assertSame([$searchField], $pendingRequest->getSearchedFields());
    }

    public function testPendingSearchRequestWithNullValues(): void
    {
        $index = new Index('empty_index');
        $queryFactory = $this->createMock(QueryFactoryInterface::class);

        $pendingRequest = new PendingSearchRequest(
            $index,
            null,
            new Range(),
            [],
            [],
            [],
            [],
            $queryFactory,
            [],
        );

        $this->assertSame($index, $pendingRequest->getIndex());
        $this->assertNull($pendingRequest->getQueryString());
        $this->assertEquals(new Range(), $pendingRequest->getRange());
        $this->assertSame([], $pendingRequest->getFieldsToFetch());
        $this->assertSame([], $pendingRequest->getAggregations());
        $this->assertSame([], $pendingRequest->getFilters());
        $this->assertSame([], $pendingRequest->getSorts());
        $this->assertSame($queryFactory, $pendingRequest->getQueryFactory());
        $this->assertSame([], $pendingRequest->getSearchedFields());
    }
}
