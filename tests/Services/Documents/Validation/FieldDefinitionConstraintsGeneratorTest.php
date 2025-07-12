<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Documents\Validation;

use Adriballa\SymfonySearchBundle\Services\Documents\Validation\FieldDefinitionConstraintsGenerator;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\BooleanField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\GeoPointField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\KeywordField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\LongField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\ObjectField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[CoversClass(FieldDefinitionConstraintsGenerator::class)]
class FieldDefinitionConstraintsGeneratorTest extends TestCase
{
    private ValidatorInterface $validator;
    private FieldDefinitionConstraintsGenerator $generator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidator();
        $this->generator = new FieldDefinitionConstraintsGenerator();
    }

    /**
     * @dataProvider stringFieldDataProvider
     * @param mixed[] $data
     */
    public function testStringField(array $data, bool $valid): void
    {
        $field = $this->mockField(FieldType::Keyword);
        $collection = new Collection([
            'fields' => [
                'string_field' => $this->generator->getConstraints($field),
            ],
        ]);

        if ($valid) {
            $this->assertCount(0, $this->validator->validate($data, $collection));
        } else {
            $this->assertGreaterThan(0, $this->validator->validate($data, $collection)->count());
        }
    }

    /**
     * @return mixed[]
     */
    public static function stringFieldDataProvider(): array
    {
        return [
            'valid value' => [
                'data' => [
                    'string_field' => 'test',
                ],
                'valid' => true,
            ],
            'wrong data' => [
                'data' => [
                    'string_field' => 4567,
                ],
                'valid' => false,
            ],
            'null data' => [
                'data' => [
                    'string_field' => null,
                ],
                'valid' => true,
            ],
            'missing data' => [
                'data' => [
                ],
                'valid' => true,
            ],
        ];
    }

    /**
     * @dataProvider integerFieldDataProvider
     * @param mixed[] $data
     */
    public function testIntField(array $data, bool $valid): void
    {
        $field = $this->mockField(FieldType::Long);
        $collection = new Collection([
            'fields' => [
                'int_field' => $this->generator->getConstraints($field),
            ],
        ]);

        if ($valid) {
            $this->assertCount(0, $this->validator->validate($data, $collection));
        } else {
            $this->assertGreaterThan(0, $this->validator->validate($data, $collection)->count());
        }
    }

    /**
     * @return mixed[]
     */
    public static function integerFieldDataProvider(): array
    {
        return [
            'valid value' => [
                'data' => [
                    'int_field' => 5678,
                ],
                'valid' => true,
            ],
            'wrong data' => [
                'data' => [
                    'int_field' => '4567',
                ],
                'valid' => false,
            ],
            'null data' => [
                'data' => [
                    'int_field' => null,
                ],
                'valid' => true,
            ],
            'missing data' => [
                'data' => [
                ],
                'valid' => true,
            ],
        ];
    }

    /**
     * @dataProvider floatFieldDataProvider
     * @param mixed[] $data
     */
    public function testFloatField(array $data, bool $valid): void
    {
        $field = $this->mockField(FieldType::Float);
        $collection = new Collection([
            'fields' => [
                'float_field' => $this->generator->getConstraints($field),
            ],
        ]);

        if ($valid) {
            $this->assertCount(0, $this->validator->validate($data, $collection));
        } else {
            $this->assertGreaterThan(0, $this->validator->validate($data, $collection)->count());
        }
    }

    /**
     * @return mixed[]
     */
    public static function floatFieldDataProvider(): array
    {
        return [
            'valid value' => [
                'data' => [
                    'float_field' => 76.0,
                ],
                'valid' => true,
            ],
            'wrong data' => [
                'data' => [
                    'float_field' => 'test',
                ],
                'valid' => false,
            ],
            'null data' => [
                'data' => [
                    'float_field' => null,
                ],
                'valid' => true,
            ],
            'missing data' => [
                'data' => [
                ],
                'valid' => true,
            ],
        ];
    }

    /**
     * @dataProvider booleanFieldDataProvider
     * @param mixed[] $data
     */
    public function testBooleanField(array $data, bool $valid): void
    {
        $field = $this->mockField(FieldType::Boolean);
        $collection = new Collection([
            'fields' => [
                'bool_field' => $this->generator->getConstraints($field),
            ],
        ]);

        if ($valid) {
            $this->assertCount(0, $this->validator->validate($data, $collection));
        } else {
            $this->assertGreaterThan(0, $this->validator->validate($data, $collection)->count());
        }
    }

    /**
     * @return mixed[]
     */
    public static function booleanFieldDataProvider(): array
    {
        return [
            'valid value' => [
                'data' => [
                    'bool_field' => true,
                ],
                'valid' => true,
            ],
            'wrong data' => [
                'data' => [
                    'bool_field' => 'true',
                ],
                'valid' => false,
            ],
            'null data' => [
                'data' => [
                    'bool_field' => null,
                ],
                'valid' => true,
            ],
            'missing data' => [
                'data' => [
                ],
                'valid' => true,
            ],
        ];
    }

    /**
     * @dataProvider dateFieldDataProvider
     * @param mixed[] $data
     */
    public function testDateField(array $data, bool $valid): void
    {
        $field = $this->mockField(FieldType::Date);
        $collection = new Collection([
            'fields' => [
                'date_field' => $this->generator->getConstraints($field),
            ],
        ]);

        if ($valid) {
            $this->assertCount(0, $this->validator->validate($data, $collection));
        } else {
            $this->assertGreaterThan(0, $this->validator->validate($data, $collection)->count());
        }
    }

    /**
     * @return mixed[]
     */
    public static function dateFieldDataProvider(): array
    {
        return [
            'valid int value' => [
                'data' => [
                    'date_field' => 4567890,
                ],
                'valid' => true,
            ],
            'valid string value' => [
                'data' => [
                    'date_field' => '2025-01-01',
                ],
                'valid' => true,
            ],
            'wrong string value' => [
                'data' => [
                    'date_field' => 'this is wrong',
                ],
                'valid' => false,
            ],
            'wrong data' => [
                'data' => [
                    'date_field' => 'true',
                ],
                'valid' => false,
            ],
            'null data' => [
                'data' => [
                    'date_field' => null,
                ],
                'valid' => true,
            ],
            'missing data' => [
                'data' => [
                ],
                'valid' => true,
            ],
        ];
    }

    /**
     * @dataProvider geoPointFieldDataProvider
     * @param mixed[] $data
     */
    public function testGeoPointField(array $data, bool $valid): void
    {
        $field = $this->mockField(FieldType::GeoPoint);
        $collection = new Collection([
            'fields' => [
                'geoPoint' => $this->generator->getConstraints($field),
            ],
        ]);

        if ($valid) {
            $this->assertCount(0, $this->validator->validate($data, $collection));
        } else {
            $this->assertGreaterThan(0, $this->validator->validate($data, $collection)->count());
        }
    }

    /**
     * @return mixed[]
     */
    public static function geoPointFieldDataProvider(): array
    {
        return [
            'valid geoPoint' => [
                'data' => [
                    'geoPoint' => [
                        'lon' => 57.765,
                        'lat' => 76.9867,
                    ],
                ],
                'valid' => true,
            ],
            'missing lat' => [
                'data' => [
                    'geoPoint' => [
                        'lat' => 76.9867,
                    ],
                ],
                'valid' => false,
            ],
            'missing lon' => [
                'data' => [
                    'geoPoint' => [
                        'lon' => 76.9867,
                    ],
                ],
                'valid' => false,
            ],
            'wrong lat' => [
                'data' => [
                    'geoPoint' => [
                        'lon' => true,
                        'lat' => 76.9867,
                    ],
                ],
                'valid' => false,
            ],
            'wrong lon' => [
                'data' => [
                    'geoPoint' => [
                        'lon' => 76.9867,
                        'lat' => 'this is lat',
                    ],
                ],
                'valid' => false,
            ],
            'extra fields' => [
                'data' => [
                    'geoPoint' => [
                        'lon' => 57.765,
                        'lat' => 76.9867,
                        'test' => 'this is a test',
                    ],
                ],
                'valid' => false,
            ],
            'missing value' => [
                'data' => [
                ],
                'valid' => true,
            ],
        ];
    }

    /**
     * @dataProvider multiPropertiesDataProvider
     * @param mixed[] $data
     */
    public function testMultiProperties(array $data, bool $valid): void
    {
        $field = new ObjectField(
            path: 'player',
            properties: [
                new KeywordField(path: 'username'),
                new LongField(path: 'score'),
                new BooleanField(path: 'active'),
                new GeoPointField(path: 'position'),
            ],
        );

        $collection = new Collection([
            'fields' => [
                'player' => $this->generator->getConstraints($field),
            ],
        ]);

        if ($valid) {
            $this->assertCount(0, $this->validator->validate($data, $collection));
        } else {
            $this->assertGreaterThan(0, $this->validator->validate($data, $collection)->count());
        }
    }

    /**
     * @return mixed[]
     */
    public static function multiPropertiesDataProvider(): array
    {
        return [
            'valid data' => [
                'data' => [
                    'player' => [
                        'username' => 'johndoe',
                        'score' => 100,
                        'active' => true,
                        'position' => [
                            'lat' => 54.0,
                            'lon' => 2.35,
                        ],
                    ],
                ],
                'valid' => true,
            ],
            'valid with missing fields' => [
                'data' => [
                    'player' => [
                        'username' => 'johndoe',
                    ],
                ],
                'valid' => true,
            ],
            'wrong username' => [
                'data' => [
                    'player' => [
                        'username' => 45678,
                        'score' => null,
                        'active' => null,
                        'position' => [
                            'lat' => 54.0,
                            'lon' => 2.35,
                        ],
                    ],
                ],
                'valid' => false,
            ],
            'wrong score' => [
                'data' => [
                    'player' => [
                        'username' => 'johndoe',
                        'score' => 'this is wrong',
                        'active' => null,
                        'position' => null,
                    ],
                ],
                'valid' => false,
            ],
            'missing lat in position' => [
                'data' => [
                    'player' => [
                        'username' => 'johndoe',
                        'score' => 56789,
                        'active' => true,
                        'position' => [
                            'lon' => 2.35,
                        ],
                    ],
                ],
                'valid' => false,
            ],
        ];
    }

    private function mockField(FieldType $type): FieldDefinitionInterface
    {
        $mock = $this->createMock(FieldDefinitionInterface::class);
        $mock->method('getType')->willReturn($type);

        return $mock;
    }
}
