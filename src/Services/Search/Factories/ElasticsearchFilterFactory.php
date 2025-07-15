<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Factories;

use Adriballa\SymfonySearchBundle\Services\Search\Filters\FilterableInterface;

class ElasticsearchFilterFactory
{
    /**
     * @param FilterableInterface[] $filters
     *
     * @return mixed[]
     */
    public function generateFilter(?array $filters): ?array
    {
        if (empty($filters)) {
            return null;
        }

        $esFilters = [];

        foreach ($filters as $filter) {
            $esFilters[] = $filter->toElasticsearchFilter();
        }

        return [
            'bool' => [
                'must' => $esFilters,
            ],
        ];
    }
}
