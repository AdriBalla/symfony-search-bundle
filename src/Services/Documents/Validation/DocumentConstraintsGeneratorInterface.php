<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Documents\Validation;

use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;
use Symfony\Component\Validator\Constraints;

interface DocumentConstraintsGeneratorInterface
{
    public function getConstraints(IndexMappingInterface $indexMapping): Constraints\Collection;
}
