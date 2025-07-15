<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\DTO;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;

class FieldInfo
{
    public function __construct(
        public string $path,
        public FieldType $type,
    ) {}
}
