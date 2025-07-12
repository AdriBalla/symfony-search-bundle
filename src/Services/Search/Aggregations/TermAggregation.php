<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Aggregations;

use Adriballa\SymfonySearchBundle\Services\Search\Filters\OrFilters;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\PrefixFilter;

class TermAggregation extends Aggregation
{
    /**
     * @param ?string[] $filters
     */
    public function __construct(
        protected string $name,
        protected int $size = self::MAX_TERMS_AGGREGATION_SIZE,
        protected ?int $minDocCount = null,
        protected ?AggregationHighlight $highlight = null,
        protected ?array $filters = null,
    ) {
        parent::__construct($name, $size, $minDocCount, $highlight);
    }

    /**
     * @return ?string[]
     */
    public function getFilters(): ?array
    {
        return $this->filters;
    }

    /**
     * @param ?string[] $filters
     */
    public function setFilters(?array $filters): void
    {
        $this->filters = $filters;
    }

    public function toElasticsearchAggregation(?string $alias = null): array
    {
        $alias ??= $this->name;

        if ($this->filters) {
            $filter = new OrFilters(
                array_map(fn ($filter) => new PrefixFilter($this->getFieldName(), $filter), $this->filters),
            );

            return [
                $alias.'__filter' => [
                    'filter' => $filter->toElasticsearchFilter(),
                    'aggs' => parent::toElasticsearchAggregation($alias),
                ],
            ];
        }

        return parent::toElasticsearchAggregation($alias);
    }
}
