<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Factories;

use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\Aggregation;
use Adriballa\SymfonySearchBundle\Services\Search\Factories\ElasticsearchAggregationFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ElasticsearchAggregationFactory::class)]
class ElasticsearchAggregationFactoryTest extends TestCase
{
    private ElasticsearchAggregationFactory $aggregationFactory;

    public function setUp(): void
    {
        $this->aggregationFactory = new ElasticsearchAggregationFactory();
    }

    public function testGenerateAggregations(): void
    {
        $aggregations = [];
        for ($i = 1; $i <= 3; ++$i) {
            $aggregation = $this->createMock(Aggregation::class);
            $aggregation->expects($this->once())
                ->method('toElasticsearchAggregation')
                ->willReturn([sprintf('aggregation_%s', $i) => $i])
            ;

            $aggregations[] = $aggregation;
        }

        $expected = [
            'aggregation_1' => 1,
            'aggregation_2' => 2,
            'aggregation_3' => 3,
        ];

        $this->assertEquals($expected, $this->aggregationFactory->generateAggregations($aggregations));
    }
}
