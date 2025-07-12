<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search;

class SearchField
{
    public function __construct(
        private readonly string $field,
        private readonly int $boost,
    ) {}

    public function getField(): string
    {
        return $this->field;
    }

    public function getBoost(): int
    {
        return $this->boost;
    }

    public function getElasticsearchFieldString(): string
    {
        return sprintf('%s^%s', $this->field, $this->boost);
    }
}
