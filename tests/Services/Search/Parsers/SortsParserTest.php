<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Parsers;

use Adriballa\SymfonySearchBundle\Services\Search\Exceptions\SortParsingException;
use Adriballa\SymfonySearchBundle\Services\Search\Parsers\Parser;
use Adriballa\SymfonySearchBundle\Services\Search\Parsers\SortsParser;
use Adriballa\SymfonySearchBundle\Services\Search\Sort\Sort;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SortsParser::class)]
#[CoversClass(Parser::class)]
class SortsParserTest extends TestCase
{
    private SortsParser $parser;

    public function setUp(): void
    {
        $this->parser = new SortsParser();
    }

    /**
     * @param string[] $input
     * @param string[] $expectedFields
     * @param string[] $expectedDirections
     * @dataProvider validSortProvider
     */
    public function testParseValidSorts(array $input, array $expectedFields, array $expectedDirections): void
    {
        $sorts = $this->parser->parse($input);

        $this->assertCount(count($expectedFields), $sorts);

        foreach ($sorts as $i => $sort) {
            $this->assertInstanceOf(Sort::class, $sort);
            $this->assertSame($expectedFields[$i], $sort->getField());
            $this->assertSame($expectedDirections[$i], $sort->getDirection()->value);
        }
    }

    /**
     * @return mixed[]
     */
    public static function validSortProvider(): array
    {
        return [
            'simple asc' => [
                'input' => ['name ASC'],
                'expectedFields' => ['name'],
                'expectedDirections' => ['asc'],
            ],
            'simple desc' => [
                'input' => ['created_at DESC'],
                'expectedFields' => ['created_at'],
                'expectedDirections' => ['desc'],
            ],
            'trimmed with dots' => [
                'input' => ['  product.price DESC '],
                'expectedFields' => ['product.price'],
                'expectedDirections' => ['desc'],
            ],
            'multiple sorts' => [
                'input' => ['name ASC', 'created_at DESC'],
                'expectedFields' => ['name', 'created_at'],
                'expectedDirections' => ['asc', 'desc'],
            ],
        ];
    }

    /**
     * @dataProvider invalidSortProvider
     * @param string[] $input
     */
    public function testParseInvalidSorts(array $input): void
    {
        $parser = new SortsParser();
        $this->expectException(SortParsingException::class);
        $parser->parse($input);
    }

    /**
     * @return mixed[]
     */
    public static function invalidSortProvider(): array
    {
        return [
            'missing direction' => [['name']],
            'invalid format' => [['name-asc']],
            'empty string' => [['']],
            'only direction' => [['ASC']],
            'bad casing' => [['name upward']],
        ];
    }
}
