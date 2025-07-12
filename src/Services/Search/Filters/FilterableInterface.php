<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Filters;

interface FilterableInterface
{
    /**
     * @return mixed[]
     */
    public function toElasticsearchFilter(): array;
}
