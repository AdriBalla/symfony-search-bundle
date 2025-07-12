<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Aggregations;

class Aggregation implements AggregationInterface
{
    public const int MAX_TERMS_AGGREGATION_SIZE = 5000;

    public function __construct(
        protected string $name,
        protected int $size = self::MAX_TERMS_AGGREGATION_SIZE,
        protected ?int $minDocCount = null,
        protected ?AggregationHighlight $highlight = null,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): Aggregation
    {
        $this->size = $size;

        return $this;
    }

    public function setName(string $name): Aggregation
    {
        $this->name = $name;

        return $this;
    }

    public function getMinDocCount(): ?int
    {
        return $this->minDocCount;
    }

    public function addSuffix(string $suffix): string
    {
        $this->name = $this->name.'.'.$suffix;

        return $this->name;
    }

    public function getAliasName(?string $alias = null): string
    {
        return $alias ?? $this->getName();
    }

    public function getFieldName(): string
    {
        return $this->name;
    }

    public function setHighlight(?AggregationHighlight $highlight): Aggregation
    {
        $this->highlight = $highlight;

        return $this;
    }

    public function getHighlight(): ?AggregationHighlight
    {
        return $this->highlight;
    }

    /**
     * @return mixed[]
     */
    public function toElasticsearchAggregation(?string $alias = null): array
    {
        $terms = [
            'field' => $this->getName(),
            'size' => $this->getSize(),
        ];

        if (null !== $this->minDocCount) {
            $terms['min_doc_count'] = $this->minDocCount;
        }

        $termQuery = [
            'terms' => $terms,
        ];

        if (null !== $this->highlight) {
            $termQuery['aggs'] = [
                'top_hit' => [
                    'top_hits' => [
                        '_source' => $this->getAliasName($alias),
                        'size' => 1,
                        'highlight' => [
                            'fields' => [
                                $this->getAliasName($alias) => $this->highlight->toElasticsearchHighlight(),
                            ],
                        ],
                    ],
                ],
            ];
        }

        return [
            $this->getAliasName($alias) => $termQuery,
        ];
    }
}
