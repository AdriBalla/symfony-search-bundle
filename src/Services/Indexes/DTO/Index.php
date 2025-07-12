<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\DTO;

class Index
{
    public function __construct(
        public string $type,
        public ?string $name = null,
    ) {}

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
