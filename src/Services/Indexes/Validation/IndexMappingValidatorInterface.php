<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Validation;

use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;

interface IndexMappingValidatorInterface
{
    public function validate(IndexMappingInterface $indexMapping): bool;
}
