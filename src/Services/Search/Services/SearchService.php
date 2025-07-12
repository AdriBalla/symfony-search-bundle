<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Services;

use Adriballa\SymfonySearchBundle\Services\Indexes\Managers\IndexNameManagerInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Factories\ElasticsearchRequestFactory;
use Adriballa\SymfonySearchBundle\Services\Search\Factories\SearchResponseFactory;
use Adriballa\SymfonySearchBundle\Services\Search\PendingSearchRequest;
use Adriballa\SymfonySearchBundle\Services\Search\SearchResponse;
use Elastic\Elasticsearch\Client;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class SearchService implements SearchServiceInterface
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly IndexNameManagerInterface $indexNameManager,
        private readonly ElasticsearchRequestFactory $elasticsearchPendingSearchRequestFactory,
        private readonly Client $client,
        private readonly SearchResponseFactory $searchResponseFactory,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function search(PendingSearchRequest $pendingSearchRequest): SearchResponse
    {
        $indexName = $this->indexNameManager->getIndexName($pendingSearchRequest->getIndex());
        $requestBody = $this->elasticsearchPendingSearchRequestFactory->getElasticsearchQuery($pendingSearchRequest);

        try {
            $result = $this->client->search([
                'index' => $indexName,
                'body' => $requestBody,
            ]);
        } catch (\Exception $exception) {
            $this->logger->error('The Elasticsearch query has failed', [
                [
                    'index' => $indexName,
                    'body' => $requestBody,
                    'error' => $exception->getMessage(),
                ],
            ]);

            return new SearchResponse($pendingSearchRequest->getIndex()->getType(), false);
        }

        return $this->searchResponseFactory->createFromResult($result->asArray(), $pendingSearchRequest);
    }
}
