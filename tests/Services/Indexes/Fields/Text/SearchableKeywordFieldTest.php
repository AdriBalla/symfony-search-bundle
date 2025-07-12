<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Fields\Text;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinition;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Text\SearchableKeywordField;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SearchableKeywordField::class)]
#[CoversClass(FieldDefinition::class)]
class SearchableKeywordFieldTest extends TestCase
{
    /**
     * @dataProvider sortableConfigurationDataProvider
     *
     * @param mixed[] $configuration
     */
    public function testGetElasticsearchConfiguration(bool $sortable, array $configuration): void
    {
        $field = new SearchableKeywordField(
            path: 'keyword.field',
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
            'sortable' => [
                'sortable' => true,
                'configuration' => [
                    'type' => FieldType::Keyword->value,
                ],
            ],
            'not sortable' => [
                'sortable' => false,
                'configuration' => [
                    'type' => FieldType::Keyword->value,
                ],
            ],
        ];
    }
}
