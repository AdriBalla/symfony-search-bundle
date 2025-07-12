<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Filters;

use Adriballa\SymfonySearchBundle\Services\Search\Filters\ExactMatchFilter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ExactMatchFilter::class)]
class ExactMatchFilterTest extends TestCase
{
    public function testConstructorAndAccessors(): void
    {
        $exactMatchFilter = new ExactMatchFilter('field', ['test', 'mock']);
        $this->assertEquals('field', $exactMatchFilter->getField());
        $this->assertEquals(['test', 'mock'], $exactMatchFilter->getValues());

        $exactMatchFilter->setField('test_setField');
        $this->assertEquals('test_setField', $exactMatchFilter->getField());
    }

    /**
     * @dataProvider exactMatchFilterDataProvider
     *
     * @param string[] $values
     * @param mixed[]  $expected
     */
    public function testToElasticsearchFilter(string $field, array $values, array $expected): void
    {
        $exactMatchFilter = new ExactMatchFilter($field, $values);
        $this->assertEquals($expected, $exactMatchFilter->toElasticsearchFilter());
    }

    /**
     * @return mixed[]
     */
    public static function exactMatchFilterDataProvider(): array
    {
        return [
            'multiple value filter' => [
                'field' => 'test_multi_value_field',
                'values' => ['test', 'mock'],
                'expected' => [
                    'terms' => [
                        'test_multi_value_field' => ['test', 'mock'],
                    ],
                ],
            ],
            'single value filter' => [
                'field' => 'test_single_value_field',
                'values' => ['value'],
                'expected' => [
                    'terms' => [
                        'test_single_value_field' => ['value'],
                    ],
                ],
            ],
        ];
    }
}
