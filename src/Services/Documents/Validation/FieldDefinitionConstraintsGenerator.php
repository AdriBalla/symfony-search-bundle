<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Documents\Validation;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\MultiPropertiesDefinitionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;

class FieldDefinitionConstraintsGenerator implements FieldDefinitionConstraintsGeneratorInterface
{
    public function getConstraints(FieldDefinitionInterface $fieldDefinition): Constraint
    {
        if ($fieldDefinition instanceof MultiPropertiesDefinitionInterface) {
            return new Constraints\Optional(
                new Constraints\Collection([
                    'fields' => array_reduce(
                        $fieldDefinition->getProperties(),
                        function (array $fields, FieldDefinitionInterface $property): array {
                            $fields[$property->getPath()] = $this->getConstraints($property);

                            return $fields;
                        },
                        [],
                    ),
                    'allowExtraFields' => false,
                    'allowMissingFields' => true,
                ]),
            );
        }

        return match ($fieldDefinition->getType()->value) {
            FieldType::Boolean->value => new Constraints\Optional(
                new Constraints\AtLeastOneOf([
                    new Constraints\IsNull(),
                    new Constraints\Type(['type' => 'bool']),
                ]),
            ),

            FieldType::Float->value,
            FieldType::Scaled_float->value => new Constraints\Optional(
                new Constraints\AtLeastOneOf([
                    new Constraints\IsNull(),
                    new Constraints\Type(['type' => 'float']),
                ]),
            ),

            FieldType::Long->value => new Constraints\Optional(
                new Constraints\AtLeastOneOf([
                    new Constraints\IsNull(),
                    new Constraints\Type(['type' => 'int']),
                ]),
            ),

            FieldType::Keyword->value,
            FieldType::SearchableText->value => new Constraints\Optional(
                new Constraints\AtLeastOneOf([
                    new Constraints\IsNull(),
                    new Constraints\Type(['type' => 'string']),
                ]),
            ),
            FieldType::Date->value => new Constraints\Optional(
                new Constraints\AtLeastOneOf([
                    new Constraints\IsNull(),
                    new Constraints\Date(),
                    new Constraints\Type(['type' => 'int']),
                ]),
            ),

            FieldType::GeoPoint->value => new Constraints\Optional(
                new Constraints\Collection([
                    'fields' => [
                        'lat' => new Constraints\Type(['type' => 'float']),
                        'lon' => new Constraints\Type(['type' => 'float']),
                    ],
                    'allowExtraFields' => false,
                    'allowMissingFields' => false,
                ]),
            ),
            default => new Constraints\AtLeastOneOf([
                new Constraints\Optional(),
            ]),
        };
    }
}
