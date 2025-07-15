<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Client;

use Adriballa\SymfonySearchBundle\Services\Search\Client\SearchClient;
use Adriballa\SymfonySearchBundle\Services\Search\PendingSearchRequest;
use Adriballa\SymfonySearchBundle\Services\Search\Sanitizers\SearchRequestSanitizerInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Sanitizers\SearchResponseSanitizerInterface;
use Adriballa\SymfonySearchBundle\Services\Search\SearchRequest;
use Adriballa\SymfonySearchBundle\Services\Search\SearchResponse;
use Adriballa\SymfonySearchBundle\Services\Search\Services\SearchServiceInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(SearchClient::class)]
class SearchClientTest extends TestCase
{
    private MockObject&SearchRequestSanitizerInterface $searchRequestSanitizer;

    private MockObject&SearchServiceInterface $searchService;

    private MockObject&SearchResponseSanitizerInterface $searchResponseSanitizer;

    private SearchClient $searchClient;

    public function setUp(): void
    {
        $this->searchRequestSanitizer = $this->createMock(SearchRequestSanitizerInterface::class);

        $this->searchService = $this->createMock(SearchServiceInterface::class);

        $this->searchResponseSanitizer = $this->createMock(SearchResponseSanitizerInterface::class);

        $this->searchClient = new SearchClient($this->searchRequestSanitizer, $this->searchService, $this->searchResponseSanitizer);
    }

    public function testSearch(): void
    {
        $searchResponse = $this->createMock(SearchResponse::class);
        $sanitizedSearchResponse = $this->createMock(SearchResponse::class);
        $pendingSearchRequest = $this->createMock(PendingSearchRequest::class);
        $searchRequest = $this->createMock(SearchRequest::class);

        $this->searchRequestSanitizer->expects($this->once())
            ->method('sanitize')
            ->with($searchRequest)
            ->willReturn($pendingSearchRequest)
        ;

        $this->searchService->expects($this->once())
            ->method('search')
            ->with($pendingSearchRequest)
            ->willReturn($searchResponse)
        ;

        $this->searchResponseSanitizer->expects($this->once())
            ->method('sanitize')
            ->with($searchResponse)
            ->willReturn($sanitizedSearchResponse)
        ;

        $this->assertEquals($sanitizedSearchResponse, $this->searchClient->search($searchRequest));
    }
}
