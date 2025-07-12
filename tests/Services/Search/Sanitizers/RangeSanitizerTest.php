<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Sanitizers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Definition\IndexDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Exceptions\SearchRangeLimitExceeded;
use Adriballa\SymfonySearchBundle\Services\Search\Range;
use Adriballa\SymfonySearchBundle\Services\Search\Sanitizers\RangeSanitizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RangeSanitizer::class)]
class RangeSanitizerTest extends TestCase
{
    private RangeSanitizer $sanitizer;

    public function setUp(): void
    {
        $this->sanitizer = new RangeSanitizer();
    }

    /**
     * @dataProvider validRangeProvider
     */
    public function testSanitizeValidRanges(?Range $range, int $paginationLimit, ?Range $expectedRange): void
    {
        $indexDefinition = $this->createMock(IndexDefinitionInterface::class);
        $indexDefinition->expects($this->any())->method('getPaginationLimit')->willReturn($paginationLimit);

        if (!$expectedRange) {
            $this->expectException(SearchRangeLimitExceeded::class);
        }

        $result = $this->sanitizer->sanitize($range, $indexDefinition);

        if ($expectedRange) {
            $this->assertEquals($expectedRange, $result);
        }
    }

    /**
     * @return mixed[]
     */
    public static function validRangeProvider(): array
    {
        return [
            'null range returns new range' => [
                'range' => null,
                'paginationLimit' => 50,
                'expectedRange' => new Range(),
            ],
            'range with no size' => [
                'range' => new Range(10, 0),
                'paginationLimit' => 100,
                'expectedRange' => new Range(10, 0),
            ],
            'range within limit' => [
                'range' => new Range(10, 20),
                'paginationLimit' => 50,
                'expectedRange' => new Range(10, 20),
            ],
            'exact limit' => [
                'range' => new Range(0, 100),
                'paginationLimit' => 100,
                'expectedRange' => new Range(0, 100),
            ],
            'out of range' => [
                'range' => new Range(2, 100),
                'paginationLimit' => 100,
                'expectedRange' => null,
            ],
        ];
    }
}
