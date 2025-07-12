<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Aggregations;

class AggregationRange
{
    /**
     * @param null|int|string $from
     * @param null|int|string $to
     */
    public function __construct(
        private string $key,
        private mixed $from,
        private mixed $to = null,
    ) {
        if (!$this->to && !$this->from) {
            throw new \InvalidArgumentException('Range should have a from or a to argument');
        }
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getFrom(): mixed
    {
        return $this->from;
    }

    public function getTo(): mixed
    {
        return $this->to;
    }

    /**
     * @return mixed[]
     */
    public function toElasticsearchAggregation(): array
    {
        $toArray = ['key' => $this->getKey()];

        if ($this->getFrom()) {
            $toArray['from'] = $this->getFrom();
        }

        if ($this->getTo()) {
            $toArray['to'] = $this->getTo();
        }

        return $toArray;
    }
}
