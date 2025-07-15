<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Filters;

interface MultipleFiltersInterface extends FilterableInterface
{
    /**
     * @return FilterableInterface[]
     */
    public function getFilters(): array;

    /**
     * @param FilterableInterface[] $filters
     */
    public function setFilters(array $filters): void;
}
