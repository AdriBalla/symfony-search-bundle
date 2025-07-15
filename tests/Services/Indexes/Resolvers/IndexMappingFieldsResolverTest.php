<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Resolvers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\MultiPropertiesDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Resolvers\IndexMappingFieldsResolver;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexMappingFieldsResolver::class)]
class IndexMappingFieldsResolverTest extends TestCase
{
    private IndexMappingFieldsResolver $resolver;

    public function setUp(): void
    {
        $this->resolver = new IndexMappingFieldsResolver();
    }

    public function testResolve(): void
    {
        $fields = [];

        $fields[] = $this->createFieldDefinitionMock('first_root_field');
        $fields[] = $this->createFieldDefinitionMock('second_root_field');
        $fields[] = $this->createFieldDefinitionMock('third_root_field');

        $firstLayerFields = [
            $this->createFieldDefinitionMock('first_field'),
            $this->createFieldDefinitionMock('second_field'),
            $this->createFieldDefinitionMock('third_field'),
        ];

        $firstLayerFields[] = $this->createMultiFieldDefinitionMock('second_level', $firstLayerFields);

        $multiField = $this->createMultiFieldDefinitionMock('first_level', $firstLayerFields);

        $fields[] = $multiField;

        $indexMapping = $this->createMock(IndexMappingInterface::class);
        $indexMapping->expects($this->any())->method('getFields')->willReturn($fields);

        $resolution = $this->resolver->resolve($indexMapping);

        $expected = [
            'first_root_field' => $fields[0],
            'second_root_field' => $fields[1],
            'third_root_field' => $fields[0],
            'first_level' => $multiField,
            'first_level.first_field' => $firstLayerFields[0],
            'first_level.second_field' => $firstLayerFields[1],
            'first_level.third_field' => $firstLayerFields[2],
            'first_level.second_level' => $firstLayerFields[3],
            'first_level.second_level.first_field' => $firstLayerFields[0],
            'first_level.second_level.second_field' => $firstLayerFields[1],
            'first_level.second_level.third_field' => $firstLayerFields[2],
        ];

        $this->assertEquals($expected, $resolution);
    }

    private function createFieldDefinitionMock(string $path): FieldDefinitionInterface
    {
        $fieldDefinition = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition->expects($this->any())->method('getPath')->willReturn($path);

        return $fieldDefinition;
    }

    /**
     * @param FieldDefinitionInterface[] $properties
     */
    private function createMultiFieldDefinitionMock(string $path, array $properties): FieldDefinitionInterface
    {
        $fieldDefinition = $this->createMock(MultiPropertiesDefinitionInterface::class);
        $fieldDefinition->expects($this->any())->method('getPath')->willReturn($path);
        $fieldDefinition->expects($this->any())->method('getProperties')->willReturn($properties);

        return $fieldDefinition;
    }
}
