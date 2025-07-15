<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Fields\Basics;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\LongField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinition;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LongField::class)]
#[CoversClass(FieldDefinition::class)]
class LongFieldTest extends FieldDefinitionTestCase
{
    /**
     * @param mixed[] $configuration
     *
     * @dataProvider sortableConfigurationDataProvider
     */
    public function testGetElasticsearchConfiguration(bool $searchable, bool $sortable, array $configuration): void
    {
        $field = new LongField(
            path: 'long.field',
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
            'With search options sortable' => [
                'searchable' => true,
                'sortable' => true,
                'configuration' => [
                    'type' => FieldType::Long->value,
                ],
            ],
            'Without search options sortable' => [
                'searchable' => false,
                'sortable' => true,
                'configuration' => [
                    'type' => FieldType::Long->value,
                ],
            ],
            'With search options not sortable' => [
                'searchable' => true,
                'sortable' => false,
                'configuration' => [
                    'type' => FieldType::Long->value,
                ],
            ],
            'Without search options not sortable' => [
                'searchable' => false,
                'sortable' => false,
                'configuration' => [
                    'type' => FieldType::Long->value,
                    'index' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider scopeDataProvider
     */
    public function testGetScope(FieldScope $scope): void
    {
        $field = new LongField(
            path : 'path',
            scope: $scope,
        );

        $this->assertEquals($scope, $field->getScope());
    }
}
