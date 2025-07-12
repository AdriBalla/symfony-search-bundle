<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\DTO;

class IndexMapping
{
    /**
     * @param mixed[] $configuration
     */
    public function __construct(
        public string $name,
        public array $configuration,
    ) {}
}
