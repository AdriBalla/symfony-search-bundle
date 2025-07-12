<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Parsers;

use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\Aggregation;
use Adriballa\SymfonySearchBundle\Services\Search\Parsers\AggregationsParser;
use Adriballa\SymfonySearchBundle\Services\Search\Parsers\Parser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AggregationsParser::class)]
#[CoversClass(Parser::class)]
class AggregationsParserTest extends TestCase
{
    private AggregationsParser $parser;

    public function setUp(): void
    {
        $this->parser = new AggregationsParser();
    }

    public function testParse(): void
    {
        $queryAggregations = [
            'test',
            '"mock"',
            '   test with spaces  ',
        ];

        $expected = [
            new Aggregation('test'),
            new Aggregation('mock'),
            new Aggregation('test with spaces'),
        ];

        $this->assertEquals($expected, $this->parser->parse($queryAggregations));
    }
}
