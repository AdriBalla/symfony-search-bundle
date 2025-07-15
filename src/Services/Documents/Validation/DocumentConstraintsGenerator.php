<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Documents\Validation;

use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;
use Symfony\Component\Validator\Constraints;

class DocumentConstraintsGenerator implements DocumentConstraintsGeneratorInterface
{
    public function __construct(
        private readonly FieldDefinitionConstraintsGeneratorInterface $fieldConstraintsGenerator,
    ) {}

    public function getConstraints(IndexMappingInterface $indexMapping): Constraints\Collection
    {
        $constraints = [];
        foreach ($indexMapping->getFields() as $field) {
            $constraints[$field->getPath()] = $this->fieldConstraintsGenerator->getConstraints($field);
        }

        return new Constraints\Collection(['fields' => $constraints]);
    }
}
