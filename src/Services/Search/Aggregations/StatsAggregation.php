<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Aggregations;

class StatsAggregation extends Aggregation
{
    /**
     * @return mixed[]
     */
    public function toElasticsearchAggregation(?string $alias = null): array
    {
        return [$this->getAliasName($alias) => ['stats' => ['field' => $this->getName()]]];
    }
}
