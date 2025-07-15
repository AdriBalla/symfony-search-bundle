<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Factories;

use Adriballa\SymfonySearchBundle\Controller\Search\Request\SearchIndexRequest;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Search\Factories\SearchRequestFactory;
use Adriballa\SymfonySearchBundle\Services\Search\Parsers\AggregationsParser;
use Adriballa\SymfonySearchBundle\Services\Search\Parsers\FiltersParser;
use Adriballa\SymfonySearchBundle\Services\Search\Parsers\SortsParser;
use Adriballa\SymfonySearchBundle\Services\Search\Range;
use Adriballa\SymfonySearchBundle\Services\Search\SearchRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(SearchRequestFactory::class)]
class SearchRequestFactoryTest extends TestCase
{
    private AggregationsParser&MockObject $aggregationsParser;
    private FiltersParser&MockObject $filtersParser;
    private MockObject&SortsParser $sortsParser;
    private SearchRequestFactory $searchRequestFactory;

    public function setUp(): void
    {
        $this->aggregationsParser = $this->createMock(AggregationsParser::class);
        $this->filtersParser = $this->createMock(FiltersParser::class);
        $this->sortsParser = $this->createMock(SortsParser::class);

        $this->searchRequestFactory = new SearchRequestFactory($this->aggregationsParser, $this->filtersParser, $this->sortsParser);
    }

    public function testCreateBuildsSearchRequestCorrectly(): void
    {
        $indexType = 'products';
        $searchFields = ['title', 'description'];
        $query = 'laptop';
        $start = 1;
        $size = 10;
        $aggregatesBy = ['brand'];
        $filters = ['category' => 'electronics'];
        $sortsBy = ['price' => 'asc'];

        $searchIndexRequest = new SearchIndexRequest(
            query: $query,
            searchFields: $searchFields,
            start: $start,
            size: $size,
            filtersBy: $filters,
            aggregatesBy: $aggregatesBy,
            sortsBy: $sortsBy,
        );

        $parsedAggregations = ['parsed_aggs'];
        $parsedFilters = ['parsed_filters'];
        $parsedSorts = ['parsed_sorts'];

        $this->aggregationsParser->expects($this->once())
            ->method('parse')
            ->with($aggregatesBy)
            ->willReturn($parsedAggregations)
        ;

        $this->filtersParser->expects($this->once())
            ->method('parse')
            ->with($filters)
            ->willReturn($parsedFilters)
        ;

        $this->sortsParser->expects($this->once())
            ->method('parse')
            ->with($sortsBy)
            ->willReturn($parsedSorts)
        ;

        $searchRequest = $this->searchRequestFactory->create($indexType, $searchIndexRequest);

        $this->assertInstanceOf(SearchRequest::class, $searchRequest);
        $this->assertSame($query, $searchRequest->getQueryString());
        $this->assertSame($searchFields, $searchRequest->getFieldsToSearch());
        $this->assertEquals(new Index($indexType), $searchRequest->getIndex());
        $this->assertEquals(new Range($start, $size), $searchRequest->getRange());
        $this->assertSame($parsedAggregations, $searchRequest->getAggregations());
        $this->assertSame($parsedFilters, $searchRequest->getFilters());
        $this->assertSame($parsedSorts, $searchRequest->getSorts());
    }
}
