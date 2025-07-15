<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Documents\DTO;

class MultipleIndexationResponse
{
    /**
     * @param null|mixed[] $errors
     */
    public function __construct(
        public readonly int $total,
        public readonly int $failure = 0,
        public readonly ?array $errors = [],
    ) {}
}
