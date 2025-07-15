<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Fields;

class SearchOptions
{
    public function __construct(private readonly int $boost = 1) {}

    public function getBoost(): int
    {
        return $this->boost;
    }
}
