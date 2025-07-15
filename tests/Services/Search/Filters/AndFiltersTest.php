<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Filters;

use Adriballa\SymfonySearchBundle\Services\Search\Filters\AndFilters;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\FilterableInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\RangeFilter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AndFilters::class)]
class AndFiltersTest extends TestCase
{
    private AndFilters $andFilters;

    /**
     * @var FilterableInterface[]
     */
    private array $filters = [];

    public function setUp(): void
    {
        $this->filters[] = $this->createMock(FilterableInterface::class);
        $this->filters[] = $this->createMock(FilterableInterface::class);

        $this->andFilters = new AndFilters($this->filters);
    }

    public function testConstructor(): void
    {
        $this->assertEquals($this->filters, $this->andFilters->getFilters());

        $filters = [new RangeFilter('price', '100', '1000')];
        $this->andFilters->setFilters($filters);
        $this->assertEquals($filters, $this->andFilters->getFilters());
    }

    public function testToElasticsearchFilter(): void
    {
        $expected = [
            'bool' => [
                'must' => array_map(function (FilterableInterface $filter) {return $filter->toElasticsearchFilter(); }, $this->filters),
            ],
        ];

        $this->assertEquals($expected, $this->andFilters->toElasticsearchFilter());
    }
}
