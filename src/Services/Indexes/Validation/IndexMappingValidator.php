<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Validation;

use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexMappingDuplicatesPathsException;
use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexMappingMissingIdException;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;

class IndexMappingValidator implements IndexMappingValidatorInterface
{
    public function validate(IndexMappingInterface $indexMapping): bool
    {
        $fieldPaths = [];
        $duplicates = [];
        $idField = null;
        foreach ($indexMapping->getFields() as $field) {
            if ('id' == $field->getPath()) {
                $idField = $field;
            }
            if (in_array($field->getPath(), $fieldPaths)) {
                $duplicates[] = $field->getPath();
            }
            $fieldPaths[] = $field->getPath();
        }

        if (null === $idField || FieldType::Keyword !== $idField->getType()) {
            throw new IndexMappingMissingIdException();
        }

        if (!empty($duplicates)) {
            throw new IndexMappingDuplicatesPathsException(array_unique($duplicates));
        }

        return true;
    }
}
