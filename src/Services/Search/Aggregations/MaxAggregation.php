<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Aggregations;

class MaxAggregation implements AggregationInterface
{
    public function __construct(
        private string $fieldName,
        private ?string $name = null,
    ) {}

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getName(): string
    {
        return $this->name ?? $this->fieldName;
    }

    /**
     * @return mixed[]
     */
    public function toElasticsearchAggregation(?string $alias = null): array
    {
        return [
            $this->getName() => ['max' => [
                'field' => $this->fieldName,
            ],
            ],
        ];
    }
}
