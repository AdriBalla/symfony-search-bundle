<?php

namespace Adriballa\SymfonySearchBundle\Services\Search;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\AggregationInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Factories\QueryFactoryInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\FilterableInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Sort\Sort;

class PendingSearchRequest
{
    /**
     * @param AggregationInterface[] $aggregations
     * @param FilterableInterface[]  $filters
     * @param Sort[]                 $sorts
     * @param string[]               $fieldsToFetch
     * @param SearchField[]          $searchedFields
     */
    public function __construct(
        private readonly Index $index,
        private readonly ?string $queryString,
        private readonly Range $range,
        private readonly array $fieldsToFetch,
        private readonly array $aggregations,
        private readonly array $filters,
        private readonly array $sorts,
        private readonly QueryFactoryInterface $queryFactory,
        private readonly array $searchedFields,
    ) {}

    public function getIndex(): Index
    {
        return $this->index;
    }

    public function getQueryString(): ?string
    {
        return $this->queryString;
    }

    public function getRange(): Range
    {
        return $this->range;
    }

    /**
     * @return string[]
     */
    public function getFieldsToFetch(): array
    {
        return $this->fieldsToFetch;
    }

    /**
     * @return AggregationInterface[]
     */
    public function getAggregations(): array
    {
        return $this->aggregations;
    }

    /**
     * @return FilterableInterface[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return Sort[]
     */
    public function getSorts(): array
    {
        return $this->sorts;
    }

    public function getQueryFactory(): QueryFactoryInterface
    {
        return $this->queryFactory;
    }

    /**
     * @return SearchField[]
     */
    public function getSearchedFields(): array
    {
        return $this->searchedFields;
    }
}
