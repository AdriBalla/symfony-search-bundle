<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search;

class Range
{
    public const DEFAULT_SIZE = 50;

    public const DEFAULT_START = 0;

    public function __construct(
        private readonly int $start = self::DEFAULT_START,
        private readonly int $size = self::DEFAULT_SIZE,
    ) {}

    public function getStart(): int
    {
        return $this->start;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}
