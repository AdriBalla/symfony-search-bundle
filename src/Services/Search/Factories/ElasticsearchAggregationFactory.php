<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Factories;

use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\AggregationInterface;

class ElasticsearchAggregationFactory
{
    /**
     * @param AggregationInterface[] $aggregations
     *
     * @return mixed[]
     */
    public function generateAggregations(array $aggregations): array
    {
        $esAggregations = [];

        foreach ($aggregations as $aggregation) {
            $esAggregations = array_replace_recursive($esAggregations, $aggregation->toElasticsearchAggregation());
        }

        return $esAggregations;
    }
}
