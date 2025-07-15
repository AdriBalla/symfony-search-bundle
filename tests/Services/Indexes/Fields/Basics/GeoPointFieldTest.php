<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Fields\Basics;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\GeoPointField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinition;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GeoPointField::class)]
#[CoversClass(FieldDefinition::class)]
class GeoPointFieldTest extends FieldDefinitionTestCase
{
    public function testGetElasticsearchConfiguration(): void
    {
        $field = new GeoPointField(
            path: 'geo.field',
            searchable: true,
        );
        $esConfig = $field->getElasticsearchConfiguration();
        $configuration = [
            'type' => 'geo_point',
        ];
        $this->assertEquals($configuration, $esConfig);
    }

    /**
     * @dataProvider scopeDataProvider
     */
    public function testGetScope(FieldScope $scope): void
    {
        $field = new GeoPointField(
            path : 'path',
            scope: $scope,
        );

        $this->assertEquals($scope, $field->getScope());
    }
}
