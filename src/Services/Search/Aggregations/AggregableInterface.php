<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Aggregations;

interface AggregableInterface
{
    /**
     * @return mixed[]
     */
    public function toElasticsearchAggregation(?string $alias = null): array;
}
