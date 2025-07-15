<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Documents\Validation;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinitionInterface;
use Symfony\Component\Validator\Constraint;

interface FieldDefinitionConstraintsGeneratorInterface
{
    public function getConstraints(FieldDefinitionInterface $fieldDefinition): Constraint;
}
