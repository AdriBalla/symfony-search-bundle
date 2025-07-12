<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Aggregations;

use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\Aggregation;
use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\AggregationHighlight;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Aggregation::class)]
class AggregationTest extends TestCase
{
    public function testAddSuffix(): void
    {
        $aggregation = new Aggregation('aggregation');
        $aggregation->addSuffix('range');

        $this->assertEquals('aggregation.range', $aggregation->getName());
    }

    public function testNameAccessors(): void
    {
        $aggregation = new Aggregation('aggregation');
        $this->assertEquals('aggregation', $aggregation->getName());

        $aggregation->setName('another_name');
        $this->assertEquals('another_name', $aggregation->getName());
    }

    public function testGetFieldName(): void
    {
        $aggregation = new Aggregation('aggregation');
        $this->assertEquals('aggregation', $aggregation->getFieldName());
    }

    public function testGetMinDocCount(): void
    {
        $aggregation = new Aggregation('aggregation', Aggregation::MAX_TERMS_AGGREGATION_SIZE, 10);
        $this->assertEquals(10, $aggregation->getMinDocCount());
    }

    public function testHighlightAccessor(): void
    {
        $highlight = $this->createMock(AggregationHighlight::class);

        $aggregation = new Aggregation('aggregation');
        $this->assertNull($aggregation->getHighlight());

        $aggregation->setHighlight($highlight);
        $this->assertEquals($highlight, $aggregation->getHighlight());
    }

    public function testSizeAccessor(): void
    {
        $aggregation = new Aggregation('aggregation', 500);
        $this->assertEquals(500, $aggregation->getSize());

        $aggregation->setSize(200);
        $this->assertEquals(200, $aggregation->getSize());
    }

    public function testToElasticsearchAggregation(): void
    {
        $aggregation = new Aggregation('aggregation', Aggregation::MAX_TERMS_AGGREGATION_SIZE, 10, new AggregationHighlight());

        $expectedEsAggregation = [
            'aggregation' => [
                'terms' => [
                    'field' => 'aggregation',
                    'size' => Aggregation::MAX_TERMS_AGGREGATION_SIZE,
                    'min_doc_count' => 10,
                ],
                'aggs' => [
                    'top_hit' => [
                        'top_hits' => [
                            '_source' => 'aggregation',
                            'size' => 1,
                            'highlight' => [
                                'fields' => [
                                    'aggregation' => [
                                        'pre_tags' => ['<em>'],
                                        'post_tags' => ['</em>'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedEsAggregation, $aggregation->toElasticsearchAggregation());
    }
}
