<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Parsers;

use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\Aggregation;

class AggregationsParser extends Parser
{
    /**
     * @param  mixed[]       $queryAggregations
     * @return Aggregation[]
     */
    public function parse(array $queryAggregations): array
    {
        $aggregations = [];
        foreach ($queryAggregations as $value) {
            $aggregations[] = new Aggregation($this->clean($value));
        }

        return $aggregations;
    }
}
