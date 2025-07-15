<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Controllers\Search\Request;

use Adriballa\SymfonySearchBundle\Controller\Search\Request\SearchIndexRequest;
use PHPUnit\Framework\TestCase;

class SearchIndexRequestTest extends TestCase
{
    /**
     * @dataProvider provideConstructorValues
     * @param string[] $searchFields
     * @param string[] $filtersBy
     * @param string[] $aggregatesBy
     * @param string[] $sortsBy
     */
    public function testConstructorSetsPropertiesCorrectly(
        ?string $query,
        array $searchFields,
        int $start,
        int $size,
        array $filtersBy,
        array $sortsBy,
        array $aggregatesBy,
    ): void {
        $searchIndexRequest = new SearchIndexRequest(
            $query,
            $searchFields,
            $start,
            $size,
            $filtersBy,
            $aggregatesBy,
            $sortsBy,
        );

        $this->assertSame($query, $searchIndexRequest->query);
        $this->assertSame($searchFields, $searchIndexRequest->searchFields);
        $this->assertSame($start, $searchIndexRequest->start);
        $this->assertSame($size, $searchIndexRequest->size);
        $this->assertSame($filtersBy, $searchIndexRequest->filtersBy);
        $this->assertSame($aggregatesBy, $searchIndexRequest->aggregatesBy);
        $this->assertSame($sortsBy, $searchIndexRequest->sortsBy);
    }

    /**
     * @return mixed[]
     */
    public static function provideConstructorValues(): array
    {
        return [
            'default values' => [
                'query' => null,
                'searchFields' => [],
                'start' => 0,
                'size' => 10,
                'filtersBy' => [],
                'sortsBy' => [],
                'aggregatesBy' => [],
            ],
            'with query and fields' => [
                'query' => 'test',
                'searchFields' => ['title'],
                'start' => 5,
                'size' => 20,
                'filtersBy' => [],
                'sortsBy' => [],
                'aggregatesBy' => [],
            ],
            'with filters and aggregates' => [
                'query' => 'query',
                'searchFields' => ['title', 'description'],
                'start' => 1,
                'size' => 15,
                'filtersBy' => ['status' => 'active'],
                'sortsBy' => ['date:desc'],
                'aggregatesBy' => ['category'],
            ],
        ];
    }
}
