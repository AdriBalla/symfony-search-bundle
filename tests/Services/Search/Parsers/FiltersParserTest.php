<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Parsers;

use Adriballa\SymfonySearchBundle\Services\Search\Filters\ExactMatchFilter;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\FilterableInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\RangeFilter;
use Adriballa\SymfonySearchBundle\Services\Search\Parsers\FiltersParser;
use Adriballa\SymfonySearchBundle\Services\Search\Parsers\Parser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FiltersParser::class)]
#[CoversClass(Parser::class)]
class FiltersParserTest extends TestCase
{
    private FiltersParser $parser;

    public function setUp(): void
    {
        $this->parser = new FiltersParser();
    }

    /**
     * @dataProvider filtersDataProvider
     *
     * @param mixed[]               $input
     * @param FilterableInterface[] $expectedFilters
     */
    public function testParse(array $input, array $expectedFilters): void
    {
        $this->assertEquals($expectedFilters, $this->parser->parse($input));
    }

    /**
     * @return mixed[]
     */
    public static function filtersDataProvider(): array
    {
        return [
            'range_filter' => [
                'input' => ['price' => '10..20'],
                'expectedFilters' => [new RangeFilter('price', '10', '20')],
            ],
            'range_filter_open_start' => [
                'input' => ['price' => '..100'],
                'expectedFilters' => [new RangeFilter('price', null, '100')],
            ],
            'exact_match_quoted' => [
                'input' => ['category' => '"Books","Tech"'],
                'expectedFilters' => [new ExactMatchFilter('category', ['Books', 'Tech'])],
            ],
            'exact_match_single_quotes' => [
                'input' => ['status' => "'active','archived'"],
                'expectedFilters' => [new ExactMatchFilter('status', ['active', 'archived'])],
            ],
            'exact_match_numeric_list' => [
                'input' => ['ids' => '1,2,3'],
                'expectedFilters' => [new ExactMatchFilter('ids', ['1', '2', '3'])],
            ],
        ];
    }
}
