<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Filters;

use Adriballa\SymfonySearchBundle\Services\Search\Filters\ExistFilter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ExistFilter::class)]
class ExistsFilterTest extends TestCase
{
    private ExistFilter $existsFilter;

    public function setUp(): void
    {
        $this->existsFilter = new ExistFilter('test_field');
    }

    public function testConstructor(): void
    {
        $this->assertEquals('test_field', $this->existsFilter->getField());
    }

    public function testSetField(): void
    {
        $this->existsFilter->setField('test_another_field');
        $this->assertEquals('test_another_field', $this->existsFilter->getField());
    }

    public function testToElasticsearchFilter(): void
    {
        $expected = [
            'exists' => [
                'field' => 'test_field',
            ],
        ];

        $this->assertEquals($expected, $this->existsFilter->toElasticsearchFilter());
    }
}
