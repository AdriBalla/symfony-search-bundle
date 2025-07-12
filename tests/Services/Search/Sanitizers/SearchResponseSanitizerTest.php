<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Sanitizers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Definition\IndexDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\BooleanField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\KeywordField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\LongField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\ObjectField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Repositories\IndexDefinitionRepositoryInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScopeService;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScopeServiceInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Sanitizers\SearchResponseSanitizer;
use Adriballa\SymfonySearchBundle\Services\Search\Sanitizers\SearchResponseSanitizerInterface;
use Adriballa\SymfonySearchBundle\Services\Search\SearchResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(SearchResponseSanitizer::class)]
class SearchResponseSanitizerTest extends TestCase
{
    private IndexDefinitionRepositoryInterface&MockObject $indexDefinitionRepository;

    private FieldScopeServiceInterface $fieldScopeService;

    private SearchResponseSanitizerInterface $sanitizer;

    public function setUp(): void
    {
        $this->indexDefinitionRepository = $this->createMock(IndexDefinitionRepositoryInterface::class);
        $this->fieldScopeService = new FieldScopeService();

        $this->sanitizer = new SearchResponseSanitizer($this->indexDefinitionRepository, $this->fieldScopeService);
    }

    /**
     * @param FieldDefinitionInterface[] $fields
     * @param mixed[]                    $hits
     * @param mixed[]                    $expected
     *
     * @dataProvider sanitizeSearchResponseDataProvider
     */
    public function testSanitize(
        array $fields,
        array $hits,
        array $expected,
    ): void {
        $searchResponse = $this->createMock(SearchResponse::class);
        $searchResponse->hits = $hits;
        $searchResponse->indexType = 'test_index';

        $indexMapping = $this->createMock(IndexMappingInterface::class);
        $indexMapping->expects($this->any())->method('getFields')->willReturn($fields);

        $indexDefinition = $this->createMock(IndexDefinitionInterface::class);
        $indexDefinition->expects($this->any())->method('getIndexMapping')->willReturn($indexMapping);

        $this->indexDefinitionRepository->expects($this->once())->method('getIndexDefinition')->willReturn($indexDefinition);

        $result = $this->sanitizer->sanitize($searchResponse);

        $this->assertEquals($expected, $result->hits);
    }

    /**
     * @return mixed[]
     */
    public static function sanitizeSearchResponseDataProvider(): array
    {
        return [
            'complex case' => [
                'fields' => [
                    new KeywordField('id'),
                    new KeywordField('username'),
                    new BooleanField('active'),
                    new LongField(path: 'score', scope: FieldScope::Private),
                    new BooleanField(path: 'promoted', scope: FieldScope::Private),
                    new ObjectField(path: 'data', properties: [
                        new LongField('height'),
                        new LongField(path: 'weight', scope: FieldScope::Private),
                    ]),
                ],
                'hits' => [
                    [
                        'id' => '1',
                        'username' => 'joe',
                        'active' => true,
                        'score' => 10,
                        'promoted' => true,
                        'data' => [
                            'height' => 165,
                            'weight' => 60,
                        ],
                    ],
                    [
                        'id' => '2',
                        'username' => 'john',
                        'active' => true,
                        'score' => 100,
                        'promoted' => false,
                        'data' => [
                            'height' => 170,
                            'weight' => 76,
                        ],
                    ],
                ],
                'expected' => [
                    [
                        'id' => '1',
                        'username' => 'joe',
                        'active' => true,
                        'data' => [
                            'height' => 165,
                        ],
                    ],
                    [
                        'id' => '2',
                        'username' => 'john',
                        'active' => true,
                        'data' => [
                            'height' => 170,
                        ],
                    ],
                ],
            ],
        ];
    }
}
