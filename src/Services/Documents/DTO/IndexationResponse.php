<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Documents\DTO;

class IndexationResponse
{
    /**
     * @param null|string[] $errors
     */
    public function __construct(
        public readonly bool $success = true,
        public readonly ?array $errors = [],
    ) {}
}
