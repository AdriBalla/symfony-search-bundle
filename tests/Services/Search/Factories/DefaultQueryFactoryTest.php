<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Factories;

use Adriballa\SymfonySearchBundle\Services\Search\Factories\DefaultQueryFactory;
use Adriballa\SymfonySearchBundle\Services\Search\PendingSearchRequest;
use Adriballa\SymfonySearchBundle\Services\Search\SearchField;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DefaultQueryFactory::class)]
class DefaultQueryFactoryTest extends TestCase
{
    private DefaultQueryFactory $queryFactory;

    public function setUp(): void
    {
        $this->queryFactory = new DefaultQueryFactory();
    }

    public function testGetQueryFromRequest(): void
    {
        $field1 = $this->createMock(SearchField::class);
        $field1->method('getElasticsearchFieldString')->willReturn('field_1');

        $field2 = $this->createMock(SearchField::class);
        $field2->method('getElasticsearchFieldString')->willReturn('field_2');

        $pendingSearchRequest = $this->createMock(PendingSearchRequest::class);
        $pendingSearchRequest->method('getSearchedFields')->willReturn([$field1, $field2]);
        $pendingSearchRequest->method('getQueryString')->willReturn('example query');

        $result = $this->queryFactory->getQueryFromRequest($pendingSearchRequest);

        $expected = [
            'bool' => [
                'should' => [
                    [
                        'multi_match' => [
                            'fields' => ['field_1', 'field_2'],
                            'operator' => 'and',
                            'query' => 'example query',
                            'boost' => 15,
                            'type' => 'phrase',
                        ],
                    ],
                    [
                        'multi_match' => [
                            'fields' => ['field_1', 'field_2'],
                            'operator' => 'and',
                            'query' => 'example query',
                            'boost' => 2,
                            'type' => 'best_fields',
                        ],
                    ],
                    [
                        'multi_match' => [
                            'fields' => ['field_1', 'field_2'],
                            'operator' => 'and',
                            'query' => 'example query',
                            'fuzziness' => 'AUTO',
                            'max_expansions' => 3,
                            'boost' => 1,
                            'type' => 'best_fields',
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        $this->assertSame($expected, $result);
    }
}
