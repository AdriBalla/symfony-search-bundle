<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Sort;

use Adriballa\SymfonySearchBundle\Services\Search\Enums\SortDirection;
use Adriballa\SymfonySearchBundle\Services\Search\Sort\Sort;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Sort::class)]
class SortTest extends TestCase
{
    /**
     * @dataProvider sortDataProvider
     */
    public function testToElasticsearchSort(string $name, SortDirection $direction): void
    {
        $sort = new Sort($name, $direction);

        $this->assertEquals($name, $sort->getField());
        $this->assertEquals($direction, $sort->getDirection());

        $expected = [
            $name => ['order' => $direction->value],
        ];

        $this->assertEquals($expected, $sort->toElasticsearchSort());

        // Test setter
        $sort->setField('updated_at');
        $this->assertSame('updated_at', $sort->getField());

        $expectedUpdated = [
            'updated_at' => ['order' => $direction->value],
        ];
        $this->assertSame($expectedUpdated, $sort->toElasticsearchSort());
    }

    /**
     * @return mixed[]
     */
    public static function sortDataProvider(): array
    {
        return [
            'ascending sort' => [
                'name' => 'ascending',
                'direction' => SortDirection::ASC,
            ],
            'descending sort' => [
                'name' => 'descending',
                'direction' => SortDirection::DESC,
            ],
        ];
    }
}
