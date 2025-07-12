<?php

declare(strict_types=1);

namespace Services\Indexes\Validation;

use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexMappingDuplicatesPathsException;
use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexMappingMissingIdException;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\KeywordField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Text\SearchableTextField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Validation\IndexMappingValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexMappingValidator::class)]
class IndexMappingValidatorTest extends TestCase
{
    private IndexMappingValidator $validator;

    public function setUp(): void
    {
        $this->validator = new IndexMappingValidator();
    }

    /**
     * @dataProvider validationDataProvider
     * @param FieldDefinitionInterface[] $fields
     */
    public function testValidate(array $fields, ?string $exception, bool $expected): void
    {
        if ($exception) {
            $this->expectException($exception);
        }

        $indexMapping = $this->createMock(IndexMappingInterface::class);
        $indexMapping->expects($this->once())->method('getFields')->willReturn($fields);

        $validation = $this->validator->validate($indexMapping);

        if (!$exception) {
            $this->assertEquals($expected, $validation);
        }
    }

    /**
     * @return mixed[]
     */
    public static function validationDataProvider(): array
    {
        return [
            'valid fields' => [
                'fields' => [
                    new KeywordField('id'),
                    new KeywordField('username'),
                    new SearchableTextField('comment'),
                ],
                'exception' => null,
                'expected' => true,
            ],
            'no id in fields' => [
                'fields' => [
                    new KeywordField('username'),
                    new SearchableTextField('comment'),
                ],
                'exception' => IndexMappingMissingIdException::class,
                'expected' => false,
            ],
            'duplicates paths in fields' => [
                'fields' => [
                    new KeywordField('id'),
                    new KeywordField('username'),
                    new SearchableTextField('comment'),
                    new SearchableTextField('username'),
                ],
                'exception' => IndexMappingDuplicatesPathsException::class,
                'expected' => false,
            ],
            'empty fields' => [
                'fields' => [
                ],
                'exception' => IndexMappingMissingIdException::class,
                'expected' => false,
            ],
        ];
    }
}
