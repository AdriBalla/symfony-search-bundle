<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Services;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Managers\IndexNameManagerInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Factories\ElasticsearchRequestFactory;
use Adriballa\SymfonySearchBundle\Services\Search\Factories\SearchResponseFactory;
use Adriballa\SymfonySearchBundle\Services\Search\PendingSearchRequest;
use Adriballa\SymfonySearchBundle\Services\Search\SearchResponse;
use Adriballa\SymfonySearchBundle\Services\Search\Services\SearchService;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Mock\Client;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

#[CoversClass(SearchService::class)]
class SearchServiceTest extends TestCase
{
    private IndexNameManagerInterface&MockObject $indexNameManager;
    private ElasticsearchRequestFactory&MockObject $elasticsearchPendingSearchRequestFactory;
    private Client $httpClient;
    private MockObject&SearchResponseFactory $searchResponseFactory;

    private SearchService $searchService;

    public function setUp(): void
    {
        $this->elasticsearchPendingSearchRequestFactory = $this->createMock(ElasticsearchRequestFactory::class);

        $this->indexNameManager = $this->createMock(IndexNameManagerInterface::class);

        $this->searchResponseFactory = $this->createMock(SearchResponseFactory::class);

        $this->httpClient = new Client();

        $client = ClientBuilder::create()
            ->setHttpClient($this->httpClient)
            ->build()
        ;

        $this->searchService = new SearchService(
            $this->indexNameManager,
            $this->elasticsearchPendingSearchRequestFactory,
            $client,
            $this->searchResponseFactory,
        );
    }

    public function testSearch(): void
    {
        $indexName = 'test_mock';
        $requestBody = ['request body'];
        $searchResult = ['search result'];

        $index = $this->createMock(Index::class);
        $pendingSearchRequest = $this->createMock(PendingSearchRequest::class);
        $searchResponse = $this->createMock(SearchResponse::class);

        $pendingSearchRequest->expects($this->once())->method('getIndex')->willReturn($index);
        $this->indexNameManager->expects($this->once())->method('getIndexName')->with($index)->willReturn($indexName);

        $this->mockResponse(SymfonyResponse::HTTP_OK, $searchResult);

        $this->elasticsearchPendingSearchRequestFactory->expects($this->once())
            ->method('getElasticsearchQuery')
            ->with($pendingSearchRequest)
            ->willReturn($requestBody)
        ;

        $this->searchResponseFactory->expects($this->once())
            ->method('createFromResult')
            ->with($searchResult)
            ->willReturn($searchResponse)
        ;

        $this->searchService->search($pendingSearchRequest);

        $lastClientRequest = $this->httpClient->getLastRequest();
        $this->assertEquals(sprintf('/%s/_search', $indexName), $lastClientRequest->getUri()->getPath());
        $this->assertEquals($requestBody, json_decode($lastClientRequest->getBody()->getContents(), true));
    }

    public function testSearchFailure(): void
    {
        $indexName = 'test_mock';

        $index = $this->createMock(Index::class);
        $pendingSearchRequest = $this->createMock(PendingSearchRequest::class);

        $pendingSearchRequest->expects($this->exactly(2))->method('getIndex')->willReturn($index);
        $this->indexNameManager->expects($this->once())->method('getIndexName')->with($index)->willReturn($indexName);

        $this->mockResponse(SymfonyResponse::HTTP_BAD_REQUEST);

        $response = $this->searchService->search($pendingSearchRequest);

        $this->assertFalse($response->success);
    }

    /**
     * @param int     $code
     * @param mixed[] $body
     */
    private function mockResponse(int $code = SymfonyResponse::HTTP_OK, array $body = []): void
    {
        $this->httpClient->addResponse(new Response($code, [Elasticsearch::HEADER_CHECK => Elasticsearch::PRODUCT_NAME, 'Content-Type' => 'application/json'], json_encode($body)));
    }
}
