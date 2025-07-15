<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Factories;

use Adriballa\SymfonySearchBundle\Services\Search\Sort\Sort;

class ElasticsearchSortFactory
{
    /**
     * @param Sort[] $sorts
     *
     * @return mixed[]
     */
    public function generateSort(array $sorts): array
    {
        $esSorts = [];

        foreach ($sorts as $sort) {
            $esSorts[] = $sort->toElasticsearchSort();
        }

        return $esSorts;
    }
}
