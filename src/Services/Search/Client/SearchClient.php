<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Client;

use Adriballa\SymfonySearchBundle\Services\Search\Sanitizers\SearchRequestSanitizerInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Sanitizers\SearchResponseSanitizerInterface;
use Adriballa\SymfonySearchBundle\Services\Search\SearchRequest;
use Adriballa\SymfonySearchBundle\Services\Search\SearchResponse;
use Adriballa\SymfonySearchBundle\Services\Search\Services\SearchServiceInterface;

class SearchClient implements SearchClientInterface
{
    public function __construct(
        private readonly SearchRequestSanitizerInterface $requestSanitizer,
        private readonly SearchServiceInterface $searchService,
        private readonly SearchResponseSanitizerInterface $searchResponseSanitizer,
    ) {}

    public function search(SearchRequest $request): SearchResponse
    {
        $pendingSearchRequest = $this->requestSanitizer->sanitize($request);

        $response = $this->searchService->search($pendingSearchRequest);

        return $this->searchResponseSanitizer->sanitize($response);
    }
}
