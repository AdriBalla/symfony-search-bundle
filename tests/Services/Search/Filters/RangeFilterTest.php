<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Filters;

use Adriballa\SymfonySearchBundle\Services\Search\Exceptions\FilterException;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\RangeFilter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RangeFilter::class)]
class RangeFilterTest extends TestCase
{
    public function testConstructorAndAccessors(): void
    {
        $rangeFilter = new RangeFilter('test', '10', '20');
        $this->assertEquals('test', $rangeFilter->getField());
        $this->assertEquals('10', $rangeFilter->getFrom());
        $this->assertEquals('20', $rangeFilter->getTo());

        $rangeFilter->setField('test_setField');
        $this->assertEquals('test_setField', $rangeFilter->getField());
    }

    /**
     * @dataProvider rangeFilterDataProvider
     *
     * @param null|mixed[] $excepted
     */
    public function testToElasticsearchFilter(?string $from, ?string $to, ?string $exception, ?array $excepted): void
    {
        if (null !== $exception) {
            $this->expectException($exception);
        }

        $rangeFilter = new RangeFilter('test', $from, $to);
        $this->assertEquals($excepted, $rangeFilter->toElasticsearchFilter());
    }

    /**
     * @return mixed[]
     */
    public static function rangeFilterDataProvider(): array
    {
        return [
            'range with no to nor from' => [
                'from' => null,
                'to' => null,
                'exception' => FilterException::class,
                'excepted' => null,
            ],
            'range with only from' => [
                'from' => '10',
                'to' => null,
                'exception' => null,
                'excepted' => [
                    'range' => [
                        'test' => [
                            'gte' => '10',
                        ],
                    ],
                ],
            ],
            'range with only to' => [
                'from' => null,
                'to' => '20',
                'exception' => null,
                'excepted' => [
                    'range' => [
                        'test' => [
                            'lte' => '20',
                        ],
                    ],
                ],
            ],
            'range with no to and from' => [
                'from' => '10',
                'to' => '20',
                'exception' => null,
                'excepted' => [
                    'range' => [
                        'test' => [
                            'gte' => '10',
                            'lte' => '20',
                        ],
                    ],
                ],
            ],
        ];
    }
}
