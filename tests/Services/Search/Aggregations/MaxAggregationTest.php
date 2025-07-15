<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Aggregations;

use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\MaxAggregation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MaxAggregation::class)]
class MaxAggregationTest extends TestCase
{
    public function testConstruct(): void
    {
        $maxAggregation = new MaxAggregation('field', 'alias');
        $this->assertEquals('field', $maxAggregation->getFieldName());
        $this->assertEquals('alias', $maxAggregation->getName());

        $maxAggregation = new MaxAggregation('field');
        $this->assertEquals('field', $maxAggregation->getFieldName());
        $this->assertEquals('field', $maxAggregation->getName());
    }

    public function testToElasticsearchAggregation(): void
    {
        $maxAggregation = new MaxAggregation('field', 'alias');

        $expected = [
            'alias' => [
                'max' => [
                    'field' => 'field',
                ],
            ],
        ];

        $this->assertEquals($expected, $maxAggregation->toElasticsearchAggregation());
    }
}
