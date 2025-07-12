<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Fields\Basics;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\KeywordField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinition;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\SearchOptions;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(KeywordField::class)]
#[CoversClass(FieldDefinition::class)]
class KeywordFieldTest extends FieldDefinitionTestCase
{
    /**
     * @param mixed[] $configuration
     *
     * @dataProvider sortableConfigurationDataProvider
     */
    public function testGetElasticsearchConfiguration(?SearchOptions $options, bool $sortable, array $configuration): void
    {
        $field = new KeywordField(
            path: 'keyword.field',
            searchOptions: $options,
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
            'With search options sortable' => [
                'options' => new SearchOptions(100),
                'sortable' => true,
                'configuration' => [
                    'type' => FieldType::Keyword->value,
                ],
            ],
            'Without search options sortable' => [
                'options' => null,
                'sortable' => true,
                'configuration' => [
                    'type' => FieldType::Keyword->value,
                ],
            ],
            'With search options not sortable' => [
                'options' => new SearchOptions(100),
                'sortable' => false,
                'configuration' => [
                    'type' => FieldType::Keyword->value,
                ],
            ],
            'Without search options not sortable' => [
                'options' => null,
                'sortable' => false,
                'configuration' => [
                    'type' => FieldType::Keyword->value,
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
        $field = new KeywordField(
            path : 'path',
            scope: $scope,
        );

        $this->assertEquals($scope, $field->getScope());
    }
}
