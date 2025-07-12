<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Aggregations;

class RangeAggregation extends Aggregation implements AggregationInterface
{
    /**
     * @param null|AggregationRange[] $ranges
     */
    public function __construct(
        string $name,
        private ?array $ranges = null,
    ) {
        parent::__construct($name);
    }

    /**
     * @return null|AggregationRange[]
     */
    public function getRanges(): ?array
    {
        return $this->ranges;
    }

    /**
     * @return mixed[]
     */
    public function toElasticsearchAggregation(?string $alias = null): array
    {
        $fieldName = preg_replace('#\.range$#', '', $this->getName());

        $ranges = [];
        if (!empty($this->getRanges())) {
            $ranges = array_map(
                function (AggregationRange $range) {
                    return $range->toElasticsearchAggregation();
                },
                $this->getRanges(),
            );
        }

        return [
            $this->getAliasName($alias) => [
                'range' => [
                    'field' => $fieldName,
                    'ranges' => $ranges,
                ],
            ],
        ];
    }
}
