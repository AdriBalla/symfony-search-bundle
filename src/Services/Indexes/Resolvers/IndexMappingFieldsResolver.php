<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Resolvers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\MultiPropertiesDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;

class IndexMappingFieldsResolver
{
    /**
     * @return mixed[]
     */
    public function resolve(IndexMappingInterface $indexMapping): array
    {
        return $this->resolveSubFields($indexMapping->getFields());
    }

    /**
     * @param FieldDefinitionInterface[] $fields
     *
     * @return mixed[]
     */
    private function resolveSubFields(array $fields): array
    {
        $flattenedFields = [];

        foreach ($fields as $field) {
            $flattenedFields[$field->getPath()] = $field;
            if ($field instanceof MultiPropertiesDefinitionInterface) {
                $subFields = $this->resolveSubFields($field->getProperties());
                foreach ($subFields as $fieldPartialPath => $subField) {
                    $flattenedFields[$field->getPath().'.'.$fieldPartialPath] = $subField;
                }
            }
        }

        return $flattenedFields;
    }
}
