<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Filters;

use Adriballa\SymfonySearchBundle\Services\Search\Filters\PrefixFilter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PrefixFilter::class)]
class PrefixFilterTest extends TestCase
{
    /**
     * @dataProvider caseSensitiveDataProvider
     */
    public function testConstructorAndAccessors(bool $caseSensitive): void
    {
        $prefixFilter = new PrefixFilter('field', 'value', $caseSensitive);
        $this->assertEquals('field', $prefixFilter->getField());
        $this->assertEquals('value', $prefixFilter->getValue());
        $this->assertEquals($caseSensitive, $prefixFilter->isCaseInsensitive());

        $prefixFilter->setField('test_setField');
        $this->assertEquals('test_setField', $prefixFilter->getField());
    }

    /**
     * @dataProvider caseSensitiveDataProvider
     */
    public function testToElasticsearchFilter(bool $caseSensitive): void
    {
        $prefixFilter = new PrefixFilter('field', 'mock', $caseSensitive);

        $expected = [
            'prefix' => [
                'field' => [
                    'value' => 'mock',
                    'case_insensitive' => $caseSensitive,
                ],
            ],
        ];

        $this->assertEquals($expected, $prefixFilter->toElasticsearchFilter());
    }

    /**
     * @return mixed[]
     */
    public static function caseSensitiveDataProvider(): array
    {
        return [
            'case sensitive enabled' => [
                'caseSensitive' => false,
            ],
            'case sensitive disabled' => [
                'caseSensitive' => false,
            ],
        ];
    }
}
