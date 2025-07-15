<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search;

use Adriballa\SymfonySearchBundle\Services\Search\Range;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Range::class)]
class RangeTest extends TestCase
{
    /**
     * @dataProvider rangeDataProvider
     */
    public function testRange(?int $start, ?int $size, Range $expectedRange): void
    {
        if (!$start && !$size) {
            $range = new Range();
        } elseif ($start && !$size) {
            $range = new Range($start);
        } else {
            $range = new Range($start, $size);
        }

        $this->assertEquals($expectedRange, $range);
        $this->assertEquals($start ?? Range::DEFAULT_START, $range->getStart());
        $this->assertEquals($size ?? Range::DEFAULT_SIZE, $range->getSize());
    }

    /**
     * @return mixed[]
     */
    public static function rangeDataProvider(): array
    {
        return [
            'start and size' => [
                'start' => 2,
                'size' => 100,
                'expectedRange' => new Range(2, 100),
            ],
            'start and no size' => [
                'start' => 2,
                'size' => null,
                'expectedRange' => new Range(2, Range::DEFAULT_SIZE),
            ],
            'no start and no size' => [
                'start' => null,
                'size' => null,
                'expectedRange' => new Range(),
            ],
        ];
    }
}
