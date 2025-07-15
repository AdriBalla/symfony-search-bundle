<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Fields\Basics;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\FloatField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinition;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FloatField::class)]
#[CoversClass(FieldDefinition::class)]
class FloatFieldTest extends FieldDefinitionTestCase
{
    /**
     * @dataProvider scopeDataProvider
     */
    public function testGetScope(FieldScope $scope): void
    {
        $field = new FloatField(
            path : 'path',
            scope: $scope,
        );

        $this->assertEquals($scope, $field->getScope());
    }

    /**
     * @param mixed[] $configuration
     *
     * @dataProvider sortableConfigurationDataProvider
     */
    public function testGetElasticsearchConfiguration(bool $sortable, bool $searchable, array $configuration): void
    {
        $field = new FloatField(
            path: 'float.field',
            sortable: $sortable,
            searchable: $searchable,
        );
        $esConfig = $field->getElasticsearchConfiguration();

        $this->assertEquals($configuration, $esConfig);
    }

    /**
     * @return mixed[]
     */
    public static function sortableConfigurationDataProvider(): array
    {
        return [
            'field is sortable and searchable' => [
                'sortable' => true,
                'searchable' => true,
                'configuration' => [
                    'type' => FieldType::Float->value,
                ],
            ],
            'field is sortable but not searchable' => [
                'sortable' => false,
                'searchable' => true,
                'configuration' => [
                    'type' => FieldType::Float->value,
                ],
            ],
            'field is not sortable but searchable' => [
                'sortable' => false,
                'searchable' => true,
                'configuration' => [
                    'type' => FieldType::Float->value,
                ],
            ],
            'field is not sortable and not searchable' => [
                'sortable' => false,
                'searchable' => false,
                'configuration' => [
                    'type' => FieldType::Float->value,
                    'index' => false,
                ],
            ],
        ];
    }
}
