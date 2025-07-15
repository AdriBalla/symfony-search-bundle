<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Filters;

use Adriballa\SymfonySearchBundle\Services\Search\Exceptions\FilterException;

class RangeFilter implements FieldFilterInterface
{
    public function __construct(
        private string $field,
        private ?string $from = null,
        private ?string $to = null,
    ) {
        if (null === $this->from && null === $this->to) {
            throw new FilterException('FilterRange needs at least a from or a to value, none provided');
        }
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function setField(string $field): void
    {
        $this->field = $field;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function getTo(): ?string
    {
        return $this->to;
    }

    /**
     * @return mixed[]
     */
    public function toElasticsearchFilter(): array
    {
        $ranges = [];

        if (null !== $this->getFrom()) {
            $ranges['gte'] = $this->getFrom();
        }

        if (null !== $this->getTo()) {
            $ranges['lte'] = $this->getTo();
        }

        return [
            'range' => [
                $this->getField() => $ranges,
            ],
        ];
    }
}
