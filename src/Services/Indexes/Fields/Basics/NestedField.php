<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\MultiPropertiesDefinition;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;

class NestedField extends MultiPropertiesDefinition
{
    /**
     * @param FieldDefinitionInterface[] $properties
     */
    public function __construct(
        string $path,
        FieldScope $scope = FieldScope::Public,
        array $properties = [],
    ) {
        parent::__construct(
            path: $path,
            type: FieldType::Nested,
            scope: $scope,
            properties: $properties,
        );
    }
}
