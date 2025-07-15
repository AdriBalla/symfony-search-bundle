<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Filters;

use Adriballa\SymfonySearchBundle\Services\Search\Filters\FilterableInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\OrFilters;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\RangeFilter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OrFilters::class)]
class OrFiltersTest extends TestCase
{
    private OrFilters $orFilters;

    /**
     * @var FilterableInterface[]
     */
    private array $filters = [];

    public function setUp(): void
    {
        $this->filters[] = $this->createMock(FilterableInterface::class);
        $this->filters[] = $this->createMock(FilterableInterface::class);

        $this->orFilters = new OrFilters($this->filters);
    }

    public function testConstructor(): void
    {
        $this->assertEquals($this->filters, $this->orFilters->getFilters());

        $filters = [new RangeFilter('price', '100', '1000')];
        $this->orFilters->setFilters($filters);
        $this->assertEquals($filters, $this->orFilters->getFilters());
    }

    public function testToElasticsearchFilter(): void
    {
        $expected = [
            'bool' => [
                'should' => array_map(function (FilterableInterface $filter) {return $filter->toElasticsearchFilter(); }, $this->filters),
                'minimum_should_match' => 1,
            ],
        ];

        $this->assertEquals($expected, $this->orFilters->toElasticsearchFilter());
    }
}
