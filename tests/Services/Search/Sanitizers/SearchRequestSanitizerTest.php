<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Sanitizers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Definition\IndexDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\PrivateIndexException;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Repositories\IndexDefinitionRepositoryInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Resolvers\IndexMappingFieldsResolver;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\IndexScope;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\IndexScopeServiceInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Factories\QueryFactoryInterface;
use Adriballa\SymfonySearchBundle\Services\Search\PendingSearchRequest;
use Adriballa\SymfonySearchBundle\Services\Search\Range;
use Adriballa\SymfonySearchBundle\Services\Search\Sanitizers\AggregationsSanitizer;
use Adriballa\SymfonySearchBundle\Services\Search\Sanitizers\FiltersSanitizer;
use Adriballa\SymfonySearchBundle\Services\Search\Sanitizers\RangeSanitizer;
use Adriballa\SymfonySearchBundle\Services\Search\Sanitizers\SearchFieldsSanitizer;
use Adriballa\SymfonySearchBundle\Services\Search\Sanitizers\SearchRequestSanitizer;
use Adriballa\SymfonySearchBundle\Services\Search\Sanitizers\SortsSanitizer;
use Adriballa\SymfonySearchBundle\Services\Search\SearchRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(SearchRequestSanitizer::class)]
class SearchRequestSanitizerTest extends TestCase
{
    private IndexDefinitionRepositoryInterface&MockObject $indexDefinitionRepository;
    private IndexScopeServiceInterface&MockObject $indexScopeService;
    private IndexMappingFieldsResolver&MockObject $indexMappingFieldsResolver;
    private AggregationsSanitizer&MockObject $aggregationsSanitizer;
    private FiltersSanitizer&MockObject $filtersSanitizer;
    private MockObject&SortsSanitizer $sortsSanitizer;
    private MockObject&RangeSanitizer $rangeSanitizer;
    private MockObject&SearchFieldsSanitizer $searchFieldSanitizer;

    private SearchRequestSanitizer $searchRequestSanitizer;

    public function setUp(): void
    {
        $this->sortsSanitizer = $this->createMock(SortsSanitizer::class);
        $this->indexDefinitionRepository = $this->createMock(IndexDefinitionRepositoryInterface::class);
        $this->indexScopeService = $this->createMock(IndexScopeServiceInterface::class);
        $this->indexMappingFieldsResolver = $this->createMock(IndexMappingFieldsResolver::class);
        $this->aggregationsSanitizer = $this->createMock(AggregationsSanitizer::class);
        $this->filtersSanitizer = $this->createMock(FiltersSanitizer::class);
        $this->rangeSanitizer = $this->createMock(RangeSanitizer::class);
        $this->searchFieldSanitizer = $this->createMock(SearchFieldsSanitizer::class);

        $this->searchRequestSanitizer = new SearchRequestSanitizer(
            $this->indexDefinitionRepository,
            $this->indexScopeService,
            $this->indexMappingFieldsResolver,
            $this->aggregationsSanitizer,
            $this->filtersSanitizer,
            $this->sortsSanitizer,
            $this->rangeSanitizer,
            $this->searchFieldSanitizer,
        );
    }

    public function testSanitizeWithValidIndexDefinitionAndScope(): void
    {
        $index = new Index('test_index');

        $queryFactory = $this->createMock(QueryFactoryInterface::class);

        $searchRequest = $this->createMock(SearchRequest::class);
        $searchRequest->method('getIndex')->willReturn($index);
        $searchRequest->method('getQueryString')->willReturn('test');
        $searchRequest->method('getFieldsToFetch')->willReturn(['fieldsToFetch']);
        $searchRequest->method('getFilters')->willReturn([]);
        $searchRequest->method('getSorts')->willReturn([]);
        $searchRequest->method('getAggregations')->willReturn([]);
        $searchRequest->method('getRange')->willReturn($this->createMock(Range::class));
        $searchRequest->method('getFieldsToSearch')->willReturn([]);
        $searchRequest->method('getQueryFactory')->willReturn($queryFactory);

        $indexDefinition = $this->createMock(IndexDefinitionInterface::class);
        $indexDefinition->method('getScope')->willReturn(IndexScope::Public);
        $indexDefinition->method('getIndexMapping')->willReturn($this->createMock(IndexMappingInterface::class));

        $this->indexDefinitionRepository
            ->expects($this->once())
            ->method('getIndexDefinition')
            ->with('test_index')
            ->willReturn($indexDefinition)
        ;

        $this->indexScopeService
            ->expects($this->once())
            ->method('isAccessible')
            ->with(IndexScope::Public)
            ->willReturn(true)
        ;

        $fields = ['title' => $this->createMock(FieldDefinitionInterface::class)];

        $this->indexMappingFieldsResolver
            ->expects($this->once())
            ->method('resolve')
            ->willReturn($fields)
        ;

        $this->aggregationsSanitizer
            ->expects($this->once())
            ->method('sanitize')
            ->willReturn(['aggregations'])
        ;

        $this->filtersSanitizer
            ->expects($this->once())
            ->method('sanitize')
            ->willReturn(['filters'])
        ;

        $this->sortsSanitizer
            ->expects($this->once())
            ->method('sanitize')
            ->willReturn(['sorts'])
        ;

        $range = $this->createMock(Range::class);
        $this->rangeSanitizer
            ->expects($this->once())
            ->method('sanitize')
            ->willReturn($range)
        ;

        $this->searchFieldSanitizer
            ->expects($this->once())
            ->method('sanitize')
            ->willReturn(['searchFields'])
        ;

        $result = $this->searchRequestSanitizer->sanitize($searchRequest);

        $this->assertInstanceOf(PendingSearchRequest::class, $result);
        $this->assertEquals('test', $result->getQueryString());
        $this->assertEquals(['filters'], $result->getFilters());
        $this->assertEquals(['sorts'], $result->getSorts());
        $this->assertEquals(['aggregations'], $result->getAggregations());
        $this->assertEquals(['searchFields'], $result->getSearchedFields());
        $this->assertEquals($range, $result->getRange());
        $this->assertEquals(['fieldsToFetch'], $result->getFieldsToFetch());
        $this->assertEquals($queryFactory, $result->getQueryFactory());
    }

    public function testSanitizeThrowsPrivateIndexException(): void
    {
        $this->expectException(PrivateIndexException::class);

        $index = new Index('private_index');
        $searchRequest = $this->createMock(SearchRequest::class);
        $searchRequest->method('getIndex')->willReturn($index);

        $indexDefinition = $this->createMock(IndexDefinitionInterface::class);
        $indexDefinition->method('getScope')->willReturn(IndexScope::Private);

        $indexDefinitionRepository = $this->createMock(IndexDefinitionRepositoryInterface::class);
        $indexDefinitionRepository->method('getIndexDefinition')->willReturn($indexDefinition);

        $indexScopeService = $this->createMock(IndexScopeServiceInterface::class);
        $indexScopeService->method('isAccessible')->willReturn(false);

        $sanitizer = new SearchRequestSanitizer(
            $indexDefinitionRepository,
            $indexScopeService,
            $this->createMock(IndexMappingFieldsResolver::class),
            $this->createMock(AggregationsSanitizer::class),
            $this->createMock(FiltersSanitizer::class),
            $this->createMock(SortsSanitizer::class),
            $this->createMock(RangeSanitizer::class),
            $this->createMock(SearchFieldsSanitizer::class),
        );

        $sanitizer->sanitize($searchRequest);
    }
}
