<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Fields\Basics;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\KeywordField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\LongField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\NestedField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinition;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\MultiPropertiesDefinition;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\SearchOptions;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(NestedField::class)]
#[CoversClass(MultiPropertiesDefinition::class)]
#[CoversClass(FieldDefinition::class)]
class NestedFieldTest extends FieldDefinitionTestCase
{
    public function testGetElasticsearchConfiguration(): void
    {
        $field = new NestedField(
            path: 'nested.field',
            scope: FieldScope::Public,
            properties: [
                new LongField(path: 'long.field', sortable: true, searchable: true),
                new KeywordField(path: 'keyword.field', searchOptions: new SearchOptions(100), sortable: true),
                new LongField(path: 'long.field.bis', sortable: false, searchable: false),
            ],
        );

        $esConfig = $field->getElasticsearchConfiguration();

        $configuration = [
            'type' => FieldType::Nested->value,
            'properties' => [
                'long.field' => [
                    'type' => FieldType::Long->value,
                ],
                'keyword.field' => [
                    'type' => FieldType::Keyword->value,
                ],
                'long.field.bis' => [
                    'type' => FieldType::Long->value,
                    'index' => false,
                ],
            ],
        ];

        $this->assertEquals($configuration, $esConfig);
    }

    /**
     * @dataProvider scopeDataProvider
     */
    public function testGetScope(FieldScope $scope): void
    {
        $field = new NestedField(
            path : 'path',
            scope: $scope,
        );

        $this->assertEquals($scope, $field->getScope());
    }
}
