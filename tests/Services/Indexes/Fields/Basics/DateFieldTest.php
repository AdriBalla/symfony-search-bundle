<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Fields\Basics;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\DateField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinition;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DateField::class)]
#[CoversClass(FieldDefinition::class)]
class DateFieldTest extends FieldDefinitionTestCase
{
    /**
     * @dataProvider scopeDataProvider
     */
    public function testGetScope(FieldScope $scope): void
    {
        $field = new DateField(
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
    public function testGetElasticsearchConfiguration(bool $sortable, FieldScope $scope, array $configuration): void
    {
        $field = new DateField(
            path: 'date.field',
            scope : $scope,
            sortable: $sortable,
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
            'date is sortable' => [
                'sortable' => true,
                'scope' => FieldScope::Public,
                'configuration' => [
                    'type' => FieldType::Date->value,
                ],
            ],
            'date is not sortable' => [
                'sortable' => false,
                'scope' => FieldScope::Private,
                'configuration' => [
                    'type' => FieldType::Date->value,
                    'index' => false,
                ],
            ],
        ];
    }
}
