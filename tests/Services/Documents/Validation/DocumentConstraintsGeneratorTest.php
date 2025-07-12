<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Documents\Validation;

use Adriballa\SymfonySearchBundle\Services\Documents\Validation\DocumentConstraintsGenerator;
use Adriballa\SymfonySearchBundle\Services\Documents\Validation\FieldDefinitionConstraintsGenerator;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints;

#[CoversClass(DocumentConstraintsGenerator::class)]
class DocumentConstraintsGeneratorTest extends TestCase
{
    private FieldDefinitionConstraintsGenerator&MockObject $fieldDefinitionConstraintsGenerator;

    private DocumentConstraintsGenerator $generator;

    public function setUp(): void
    {
        $this->fieldDefinitionConstraintsGenerator = $this->createMock(FieldDefinitionConstraintsGenerator::class);

        $this->generator = new DocumentConstraintsGenerator($this->fieldDefinitionConstraintsGenerator);
    }

    public function testGetConstraintsReturnsCorrectCollection(): void
    {
        $field1 = $this->createMock(FieldDefinitionInterface::class);
        $field1->method('getPath')->willReturn('title');

        $field2 = $this->createMock(FieldDefinitionInterface::class);
        $field2->method('getPath')->willReturn('price');

        $indexMapping = $this->createMock(IndexMappingInterface::class);
        $indexMapping->expects($this->once())->method('getFields')->willReturn([$field1, $field2]);

        $constraintsForField1 = new Constraints\Sequentially(
            [
                new Constraints\Optional(),
                new Constraints\Type(['type' => 'string']),
            ],
        );

        $constraintsForField2 = new Constraints\Sequentially(
            [
                new Constraints\Optional(),
                new Constraints\Type(['type' => 'float']),
            ],
        );

        $this->fieldDefinitionConstraintsGenerator
            ->expects($this->exactly(2))
            ->method('getConstraints')
            ->willReturnCallback(function (FieldDefinitionInterface $field) use (
                $field1,
                $field2,
                $constraintsForField1,
                $constraintsForField2
            ) {
                if ($field === $field1) {
                    return $constraintsForField1;
                }
                if ($field === $field2) {
                    return $constraintsForField2;
                }

                throw new \InvalidArgumentException('Unexpected field');
            })
        ;

        $result = $this->generator->getConstraints($indexMapping);

        $expectedResult = new Constraints\Collection(
            [
                'title' => $constraintsForField1,
                'price' => $constraintsForField2,
            ],
        );
        $this->assertEquals($expectedResult, $result);
    }
}
