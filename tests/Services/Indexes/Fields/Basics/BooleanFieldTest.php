<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Fields\Basics;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\BooleanField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinition;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(BooleanField::class)]
#[CoversClass(FieldDefinition::class)]
class BooleanFieldTest extends FieldDefinitionTestCase
{
    /**
     * @dataProvider scopeDataProvider
     */
    public function testGetScope(FieldScope $scope): void
    {
        $field = new BooleanField(
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
        $field = new BooleanField(
            path: 'boolean.field',
            scope: $scope,
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
            'boolean is sortable' => [
                'sortable' => true,
                'scope' => FieldScope::Public,
                'configuration' => [
                    'type' => FieldType::Boolean->value,
                ],
            ],
            'boolean is not sortable' => [
                'sortable' => false,
                'scope' => FieldScope::Private,
                'configuration' => [
                    'type' => FieldType::Boolean->value,
                    'index' => false,
                ],
            ],
        ];
    }
}
