<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\AggregationInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Factories\QueryFactoryInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\FilterableInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Sort\Sort;

class SearchRequest
{
    /**
     * @param string[]               $fieldsToSearch
     * @param string[]               $fieldsToFetch
     * @param AggregationInterface[] $aggregations
     * @param FilterableInterface[]  $filters
     * @param Sort[]                 $sorts
     */
    public function __construct(
        private readonly Index $index,
        private readonly ?string $queryString,
        private readonly ?Range $range = new Range(),
        private readonly array $fieldsToSearch = [],
        private readonly array $fieldsToFetch = [],
        private readonly array $aggregations = [],
        private readonly array $filters = [],
        private readonly array $sorts = [],
        private readonly ?QueryFactoryInterface $queryFactory = null,
    ) {}

    public function getIndex(): Index
    {
        return $this->index;
    }

    public function getQueryString(): ?string
    {
        return $this->queryString;
    }

    /**
     * @return string[]
     */
    public function getFieldsToSearch(): array
    {
        return $this->fieldsToSearch;
    }

    /**
     * @return string[]
     */
    public function getFieldsToFetch(): array
    {
        return $this->fieldsToFetch;
    }

    public function getQueryFactory(): ?QueryFactoryInterface
    {
        return $this->queryFactory;
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

    public function getRange(): ?Range
    {
        return $this->range;
    }
}
