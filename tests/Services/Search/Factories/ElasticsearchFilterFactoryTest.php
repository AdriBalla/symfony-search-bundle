<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Factories;

use Adriballa\SymfonySearchBundle\Services\Search\Factories\ElasticsearchFilterFactory;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\FilterableInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ElasticsearchFilterFactory::class)]
class ElasticsearchFilterFactoryTest extends TestCase
{
    private ElasticsearchFilterFactory $elasticsearchFilterFactory;

    public function setUp(): void
    {
        $this->elasticsearchFilterFactory = new ElasticsearchFilterFactory();
    }

    public function testGenerateFilter(): void
    {
        $filters = [];
        for ($i = 1; $i <= 3; ++$i) {
            $filter = $this->createMock(FilterableInterface::class);
            $filter->expects($this->once())
                ->method('toElasticsearchFilter')
                ->willReturn([sprintf('filter_%s', $i) => $i])
            ;

            $filters[] = $filter;
        }

        $expected = [
            'bool' => [
                'must' => [
                    ['filter_1' => 1],
                    ['filter_2' => 2],
                    ['filter_3' => 3],
                ],
            ],
        ];

        $this->assertEquals($expected, $this->elasticsearchFilterFactory->generateFilter($filters));
    }

    public function testGenerateEmptyFilter(): void
    {
        $this->assertNull($this->elasticsearchFilterFactory->generateFilter([]));
    }
}
