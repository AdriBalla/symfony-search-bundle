<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Factories;

use Adriballa\SymfonySearchBundle\Controller\Search\Request\SearchIndexRequest;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Search\Parsers\AggregationsParser;
use Adriballa\SymfonySearchBundle\Services\Search\Parsers\FiltersParser;
use Adriballa\SymfonySearchBundle\Services\Search\Parsers\SortsParser;
use Adriballa\SymfonySearchBundle\Services\Search\Range;
use Adriballa\SymfonySearchBundle\Services\Search\SearchRequest;

class SearchRequestFactory implements SearchRequestFactoryInterface
{
    public function __construct(
        private readonly AggregationsParser $aggregationsParser,
        private readonly FiltersParser $filtersParser,
        private readonly SortsParser $sortsParser,
    ) {}

    public function create(string $indexType, SearchIndexRequest $searchIndexRequest): SearchRequest
    {
        return new SearchRequest(
            new Index($indexType),
            $searchIndexRequest->query,
            new Range($searchIndexRequest->start, $searchIndexRequest->size),
            $searchIndexRequest->searchFields,
            [],
            $this->aggregationsParser->parse($searchIndexRequest->aggregatesBy),
            $this->filtersParser->parse($searchIndexRequest->filtersBy),
            $this->sortsParser->parse($searchIndexRequest->sortsBy),
        );
    }
}
