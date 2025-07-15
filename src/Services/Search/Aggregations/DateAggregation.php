<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Aggregations;

class DateAggregation extends RangeAggregation
{
    public const MISSING_VALUE = '1969-01-01';

    /**
     * @param null|AggregationRange[] $ranges
     */
    public function __construct(
        string $name,
        ?array $ranges = null,
        private readonly ?string $format = null,
        private readonly ?string $missing = self::MISSING_VALUE,
    ) {
        parent::__construct($name, $ranges);
    }

    /**
     * @return array<mixed>
     */
    public function toElasticsearchAggregation(?string $alias = null): array
    {
        if (empty($this->getRanges())) {
            $ranges = [
            ];
        } else {
            $ranges = array_map(
                function (AggregationRange $range) {
                    return $range->toElasticsearchAggregation();
                },
                $this->getRanges(),
            );
        }

        return [
            $this->getAliasName($alias) => [
                'date_range' => [
                    'field' => $this->getName(),
                    'ranges' => $ranges,
                    ...(isset($this->format) ? ['format' => $this->format] : []),
                    ...(isset($this->missing) ? ['missing' => $this->missing] : []),
                ],
            ],
        ];
    }
}
