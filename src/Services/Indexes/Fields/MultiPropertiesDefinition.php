<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Fields;

use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;

abstract class MultiPropertiesDefinition extends FieldDefinition implements MultiPropertiesDefinitionInterface
{
    /**
     * @param FieldDefinitionInterface[] $properties
     */
    public function __construct(
        string $path,
        FieldType $type,
        FieldScope $scope = FieldScope::Public,
        private readonly array $properties = [],
    ) {
        parent::__construct(
            path: $path,
            type: $type,
            scope: $scope,
        );
    }

    /**
     * @return FieldDefinitionInterface[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return mixed[]
     */
    public function getElasticsearchConfiguration(): array
    {
        $config = [
            'type' => $this->getType()->value,
            'properties' => [],
        ];

        foreach ($this->getProperties() as $property) {
            $config['properties'][$property->getPath()] = $property->getElasticsearchConfiguration();
        }

        return $config;
    }
}
