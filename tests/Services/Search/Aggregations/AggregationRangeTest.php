<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Aggregations;

use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\AggregationRange;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AggregationRange::class)]
class AggregationRangeTest extends TestCase
{
    /**
     * @dataProvider aggregationRangeDataProvider
     *
     * @param null|int|string $from
     * @param null|int|string $to
     */
    public function testAggregationRange(string $key, mixed $from, mixed $to, ?string $exception): void
    {
        if ($exception) {
            $this->expectException($exception);
        }

        $aggregation = new AggregationRange($key, $from, $to);

        if (!$exception) {
            $expected = ['key' => $key];

            if ($from) {
                $expected['from'] = $from;
            }

            if ($to) {
                $expected['to'] = $to;
            }

            $this->assertEquals($to, $aggregation->getTo());
            $this->assertEquals($from, $aggregation->getFrom());
            $this->assertEquals($key, $aggregation->getKey());
            $this->assertEquals($expected, $aggregation->toElasticsearchAggregation());
        }
    }

    /**
     * @return mixed[]
     */
    public static function aggregationRangeDataProvider(): array
    {
        return [
            'range without from' => [
                'key' => 'test_without_from',
                'from' => null,
                'to' => 1000,
                'exception' => null,
            ],
            'range without to' => [
                'key' => 'test_without_from',
                'from' => 2000,
                'to' => null,
                'exception' => null,
            ],
            'invalid range' => [
                'key' => 'test_without_from',
                'from' => null,
                'to' => null,
                'exception' => \InvalidArgumentException::class,
            ],
            'range with string boundaries' => [
                'key' => 'test_without_from',
                'from' => 'now-1d/d',
                'to' => 'now',
                'exception' => null,
            ],
        ];
    }
}
