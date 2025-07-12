<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Filters;

class AndFilters implements MultipleFiltersInterface
{
    /**
     * @param FilterableInterface[] $filters
     */
    public function __construct(
        private array $filters,
    ) {}

    /**
     * @return FilterableInterface[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param FilterableInterface[] $filters
     */
    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    /**
     * @return mixed[]
     */
    public function toElasticsearchFilter(): array
    {
        return [
            'bool' => [
                'must' => array_map(function (FilterableInterface $filter) {return $filter->toElasticsearchFilter(); }, $this->getFilters()),
            ],
        ];
    }
}
