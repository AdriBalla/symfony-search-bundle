<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Filters;

use Adriballa\SymfonySearchBundle\Services\Search\Filters\ExcludeFilter;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\FilterableInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ExcludeFilter::class)]
class ExcludeFilterTest extends TestCase
{
    private ExcludeFilter $excludeFilter;

    private FilterableInterface $filterable;

    public function setUp(): void
    {
        $this->filterable = $this->createMock(FilterableInterface::class);
        $this->excludeFilter = new ExcludeFilter($this->filterable);
    }

    public function testConstructor(): void
    {
        $this->assertEquals($this->filterable, $this->excludeFilter->getFilter());
    }

    public function testToElasticsearchFilter(): void
    {
        $this->assertEquals(
            [
                'bool' => [
                    'must_not' => [
                        $this->filterable->toElasticsearchFilter(),
                    ],
                ],
            ],
            $this->excludeFilter->toElasticsearchFilter(),
        );
    }
}
