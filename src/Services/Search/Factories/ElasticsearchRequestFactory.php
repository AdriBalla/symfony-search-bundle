<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Factories;

use Adriballa\SymfonySearchBundle\Services\Search\PendingSearchRequest;

class ElasticsearchRequestFactory
{
    public function __construct(
        private readonly ElasticsearchAggregationFactory $aggregationFactory,
        private readonly ElasticsearchFilterFactory $filterFactory,
        private readonly ElasticsearchSortFactory $sortFactory,
    ) {}

    /**
     * @return mixed[]
     */
    public function getElasticsearchQuery(PendingSearchRequest $searchRequest): array
    {
        $rawQuery = [];
        $filters = [];
        $textQuery = null;

        if (!empty($searchRequest->getQueryString())) {
            $textQuery = $searchRequest->getQueryFactory()->getQueryFromRequest($searchRequest);
        }

        if (!empty($searchRequest->getFilters())) {
            $filters = $this->filterFactory->generateFilter($searchRequest->getFilters());
        }

        if (empty($filters) && null === $textQuery) {
            $filters = [
                'match_all' => new \stdClass(),
            ];
        }

        if (null !== $textQuery) {
            $queries = isset($textQuery['bool']) ? $textQuery : ['bool' => ['must' => $textQuery]];
        }

        $queries['bool']['filter'] = $filters;

        if (!empty($searchRequest->getSorts())) {
            $rawQuery['sort'] = $this->sortFactory->generateSort($searchRequest->getSorts());
        }

        if (!empty($searchRequest->getAggregations())) {
            $rawQuery['aggs'] = $this->aggregationFactory->generateAggregations($searchRequest->getAggregations());
        }

        if (!empty($searchRequest->getFieldsToFetch())) {
            $rawQuery['_source'] = ['includes' => $searchRequest->getFieldsToFetch()];
        }

        $rawQuery['from'] = $searchRequest->getRange()->getStart();
        $rawQuery['size'] = $searchRequest->getRange()->getSize();

        $rawQuery['query'] = $queries;

        return $rawQuery;
    }
}
