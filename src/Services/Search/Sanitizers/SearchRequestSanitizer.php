<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Sanitizers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\PrivateIndexException;
use Adriballa\SymfonySearchBundle\Services\Indexes\Repositories\IndexDefinitionRepositoryInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Resolvers\IndexMappingFieldsResolver;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\IndexScopeServiceInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Factories\DefaultQueryFactory;
use Adriballa\SymfonySearchBundle\Services\Search\PendingSearchRequest;
use Adriballa\SymfonySearchBundle\Services\Search\SearchRequest;

class SearchRequestSanitizer implements SearchRequestSanitizerInterface
{
    public function __construct(
        private readonly IndexDefinitionRepositoryInterface $indexDefinitionRepository,
        private readonly IndexScopeServiceInterface $indexScopeService,
        private readonly IndexMappingFieldsResolver $indexMappingFieldsResolver,
        private readonly AggregationsSanitizer $aggregationsSanitizer,
        private readonly FiltersSanitizer $filtersSanitizer,
        private readonly SortsSanitizer $sortsSanitizer,
        private readonly RangeSanitizer $rangeSanitizer,
        private readonly SearchFieldsSanitizer $searchFieldSanitizer,
    ) {}

    public function sanitize(SearchRequest $searchRequest): PendingSearchRequest
    {
        $indexDefinition = $this->indexDefinitionRepository->getIndexDefinition($searchRequest->getIndex()->getType());

        if (!$this->indexScopeService->isAccessible($indexDefinition->getScope())) {
            throw new PrivateIndexException($searchRequest->getIndex());
        }

        $fieldDefinitions = $this->indexMappingFieldsResolver->resolve($indexDefinition->getIndexMapping());

        $filters = $this->filtersSanitizer->sanitize($searchRequest->getFilters(), $fieldDefinitions);
        $sorts = $this->sortsSanitizer->sanitize($searchRequest->getSorts(), $fieldDefinitions);
        $aggregations = $this->aggregationsSanitizer->sanitize($searchRequest->getAggregations(), $fieldDefinitions);
        $range = $this->rangeSanitizer->sanitize($searchRequest->getRange(), $indexDefinition);
        $searchedFields = $this->searchFieldSanitizer->sanitize($searchRequest->getFieldsToSearch(), $fieldDefinitions);
        $queryFactory = $searchRequest->getQueryFactory() ?? new DefaultQueryFactory();

        return new PendingSearchRequest(
            $searchRequest->getIndex(),
            $searchRequest->getQueryString(),
            $range,
            $searchRequest->getFieldsToFetch(),
            $aggregations,
            $filters,
            $sorts,
            $queryFactory,
            $searchedFields,
        );
    }
}
