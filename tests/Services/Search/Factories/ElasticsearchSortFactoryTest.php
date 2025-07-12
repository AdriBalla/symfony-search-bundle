<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Factories;

use Adriballa\SymfonySearchBundle\Services\Search\Factories\ElasticsearchSortFactory;
use Adriballa\SymfonySearchBundle\Services\Search\Sort\Sort;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ElasticsearchSortFactory::class)]
class ElasticsearchSortFactoryTest extends TestCase
{
    private ElasticsearchSortFactory $elasticsearchSortFactory;

    public function setUp(): void
    {
        $this->elasticsearchSortFactory = new ElasticsearchSortFactory();
    }

    public function testGenerateFilter(): void
    {
        $sorts = [];
        for ($i = 1; $i <= 3; ++$i) {
            $sort = $this->createMock(Sort::class);
            $sort->expects($this->once())
                ->method('toElasticsearchSort')
                ->willReturn([sprintf('sort_%s', $i) => $i])
            ;

            $sorts[] = $sort;
        }

        $expected = [
            ['sort_1' => 1],
            ['sort_2' => 2],
            ['sort_3' => 3],
        ];

        $this->assertEquals($expected, $this->elasticsearchSortFactory->generateSort($sorts));
    }
}
