<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Factories;

use Adriballa\SymfonySearchBundle\Services\Search\PendingSearchRequest;
use Adriballa\SymfonySearchBundle\Services\Search\SearchResponse;

class SearchResponseFactory
{
    /**
     * @param mixed[] $result
     */
    public function createFromResult(array $result, PendingSearchRequest $searchRequest): SearchResponse
    {
        return new SearchResponse(
            $searchRequest->getIndex()->getType(),
            true,
            $searchRequest->getRange()->getStart(),
            $searchRequest->getRange()->getSize(),
            (int) $result['took'],
            (int) $result['hits']['total']['value'],
            array_column($result['hits']['hits'], '_source'),
            $this->getAggregations($result['aggregations'] ?? []),
        );
    }

    /**
     * @param  mixed[] $aggs
     * @return mixed[]
     */
    private function getAggregations(array $aggs): array
    {
        $aggregations = [];
        foreach ($aggs as $key => $agg) {
            if (isset($agg['buckets'])) {
                $keyword = preg_replace('#\.(keyword)$#', '', $key);
                foreach ($agg['buckets'] as $bucket) {
                    $aggregations[$keyword][$bucket['key']] = $bucket['doc_count'];
                }
            }

            if (is_array($agg)) {
                $aggregations = array_merge($aggregations, $this->getAggregations($agg));
            }
        }

        return $aggregations;
    }
}
