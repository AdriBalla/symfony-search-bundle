<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Manager;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\IndexMapping;
use Adriballa\SymfonySearchBundle\Services\Indexes\Managers\IndexManager;
use Adriballa\SymfonySearchBundle\Services\Indexes\Managers\IndexNameManagerInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Setting\IndexSettings;
use Adriballa\SymfonySearchBundle\Services\Indexes\Transformers\IndexSettingsTransformer;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Client\Exception\HttpException;
use Http\Mock\Client;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

#[CoversClass(IndexManager::class)]
class IndexManagerTest extends TestCase
{
    private IndexNameManagerInterface&MockObject $indexNameManager;

    private IndexSettingsTransformer&MockObject $indexSettingsTransformer;

    private Client $httpClient;

    private IndexManager $indexManager;

    protected function setUp(): void
    {
        $this->indexNameManager = $this->createMock(IndexNameManagerInterface::class);

        $this->indexSettingsTransformer = $this->createMock(IndexSettingsTransformer::class);

        $this->httpClient = new Client();

        $client = ClientBuilder::create()
            ->setHttpClient($this->httpClient)
            ->build()
        ;

        $this->indexManager = new IndexManager(
            $this->indexNameManager,
            $this->indexSettingsTransformer,
            $client,
        );
    }

    /**
     * @dataProvider  indexExistsDataProvider
     */
    public function testIndexExists(Index $index, bool $exists): void
    {
        $this->mockResponse($exists ? SymfonyResponse::HTTP_OK : SymfonyResponse::HTTP_NOT_FOUND);

        $hasIndex = $this->indexManager->indexExists($index);

        $this->assertEquals('/'.$index->getName(), $this->httpClient->getLastRequest()->getUri()->getPath());
        $this->assertEquals($exists, $hasIndex);
    }

    /**
     * @return mixed[]
     */
    public static function indexExistsDataProvider(): array
    {
        return [
            'index is missing' => [
                'index' => new Index('mocks'),
                'exists' => false,
            ],
            'index exists' => [
                'index' => new Index('mocks'),
                'exists' => true,
            ],
            'index missing raw' => [
                'index' => new Index('mocks', 'index-mocks'),
                'exists' => false,
            ],
            'index exists raw' => [
                'index' => new Index('mocks', 'index-mocks'),
                'exists' => true,
            ],
        ];
    }

    public function testCreateIndex(): void
    {
        $suffix = 'createIndex';
        $indexName = 'test_alias_mock';
        $configuration = ['this is the configuration'];

        $indexMapping = $this->createMock(IndexMapping::class);
        $indexMapping->name = 'test_mocks';
        $indexMapping->configuration = $configuration;

        $this->indexNameManager->expects($this->once())
            ->method('getIndexNameForType')
            ->with($indexMapping->name)
            ->willReturn($indexName)
        ;

        $this->mockResponse();

        $this->indexManager->createIndex($indexMapping, $suffix);

        $this->assertEquals(sprintf('/%s', $indexName), $this->httpClient->getLastRequest()->getUri()->getPath());
        $this->assertEquals('PUT', $this->httpClient->getLastRequest()->getMethod());
        $this->assertEquals($configuration, json_decode($this->httpClient->getLastRequest()->getBody()->getContents()));
    }

    public function testCopyIndex(): void
    {
        $source = 'index_source';
        $destination = 'index_destination';
        $this->mockResponse();

        $this->indexManager->copyIndex($source, $destination);

        $requests = $this->httpClient->getRequests();
        $this->assertEquals('POST', $requests[0]->getMethod());
        $this->assertEquals('/_reindex', $requests[0]->getUri()->getPath());
        $this->assertEquals(
            [
                'source' => ['index' => $source],
                'dest' => ['index' => $destination],
            ],
            json_decode($requests[0]->getBody()->getContents(), true),
        );
    }

    public function testRefreshIndex(): void
    {
        $this->mockResponse();

        $name = 'test_alias_mock';

        $index = $this->createMock(Index::class);
        $index->expects($this->exactly(2))
            ->method('getName')
            ->willReturn($name)
        ;

        $this->indexManager->refreshIndex($index);

        // Assertions
        $requests = $this->httpClient->getRequests();

        $this->assertCount(1, $requests);
        $this->assertEquals('POST', $requests[0]->getMethod());
        $this->assertEquals(sprintf('/%s/_refresh', $name), $requests[0]->getUri()->getPath());
    }

    public function testSetIndexSetting(): void
    {
        $this->mockResponse();

        $index = $this->createMock(Index::class);
        $settings = $this->createMock(IndexSettings::class);
        $indexName = 'test_mock';
        $settingsConfiguration = ['settings' => 'value'];

        $this->indexNameManager->expects($this->once())
            ->method('getIndexName')
            ->with($index)
            ->willReturn($indexName)
        ;

        $this->indexSettingsTransformer->expects($this->once())
            ->method('transform')
            ->with($settings)
            ->willReturn($settingsConfiguration)
        ;

        $this->indexManager->setIndexSetting(
            $index,
            $settings,
        );

        $requests = $this->httpClient->getRequests();

        $this->assertCount(1, $requests);
        $request = $requests[0];
        $this->assertEquals(sprintf('/%s/_settings', $indexName), $request->getUri()->getPath());
        $this->assertEquals($settingsConfiguration, json_decode($request->getBody()->getContents(), true));
    }

    /**
     * @dataProvider deleteDataProvider
     *
     * @param bool[] $response
     */
    public function testDeleteIndex(Index $index, array $response, bool $exception, bool $expected): void
    {
        $indexName = $index->getName();
        if (null === $index->getName()) {
            $aliasName = 'alias_name';
            $this->indexNameManager->expects($this->once())
                ->method('getAliasName')
                ->with($index->getType())
                ->willReturn($aliasName)
            ;

            $indexName = 'index_name';

            if ($exception) {
                $this->mockResponse(SymfonyResponse::HTTP_BAD_REQUEST);
            } else {
                $aliasResponse = [
                    'index_name' => 'alias_name',
                ];

                $this->mockResponse(SymfonyResponse::HTTP_OK, $aliasResponse);
            }
        }

        $this->mockResponse(SymfonyResponse::HTTP_OK, $response);

        $delete = $this->indexManager->deleteIndex($index);

        if ($expected) {
            $this->assertTrue($delete);
            $request = $this->httpClient->getLastRequest();
            $this->assertEquals('DELETE', $request->getMethod());
            $this->assertEquals('/'.$indexName, $request->getUri()->getPath());
            $this->assertEquals('', (string) $request->getBody());
        } else {
            $this->assertFalse($delete);
        }
    }

    /**
     * @return mixed[]
     */
    public static function deleteDataProvider(): array
    {
        return [
            'delete success with index name' => [
                'index' => new Index('test_type', 'test_name'),
                'response' => ['acknowledged' => true],
                'exception' => false,
                'expected' => true,
            ],
            'delete success with index type' => [
                'index' => new Index('test_type'),
                'response' => ['acknowledged' => true],
                'exception' => false,
                'expected' => true,
            ],
            'delete failure' => [
                'index' => new Index('test_type', 'test_name'),
                'response' => ['error' => true],
                'exception' => false,
                'expected' => false,
            ],
            'delete success with index type and failure' => [
                'index' => new Index('test_type'),
                'response' => ['acknowledged' => true],
                'exception' => true,
                'expected' => false,
            ],
        ];
    }

    /**
     * @dataProvider addAliasDataProvider
     */
    public function testAddAliasOnIndex(bool $hasAlias, bool $exception, bool $delete): void
    {
        $indexType = 'mock';
        $indexName = 'test_mock';
        $indexToDelete = 'to_delete_mock';
        $aliasName = 'alias_mock';

        $index = new Index($indexType, $indexName);

        if ($exception) {
            $this->httpClient->addException($this->createMock(HttpException::class));
        } else {
            $response = [];
            if ($hasAlias) {
                $response = [
                    $indexToDelete => [
                        'aliases' => [
                            $aliasName => [],
                        ],
                    ],
                ];
            }

            $this->mockResponse(SymfonyResponse::HTTP_OK, $response);
        }

        $this->mockResponse();

        if ($delete) {
            $this->mockResponse();
        }

        // Test call
        $this->indexManager->addAliasOnIndex(
            $index,
            $delete,
        );

        // Assertions
        $requests = $this->httpClient->getRequests();

        $this->assertEquals('GET', $requests[0]->getMethod());
        $this->assertEquals('/_alias/', $requests[0]->getUri()->getPath());

        $this->assertEquals('POST', $requests[1]->getMethod());
        $this->assertEquals('/_aliases', $requests[1]->getUri()->getPath());

        if (!$hasAlias || $exception) {
            $this->assertEquals([
                'actions' => [
                    ['add' => ['index' => $indexName, 'alias' => '']],
                ],
            ], json_decode($requests[1]->getBody()->getContents(), true));
        } else {
            $this->assertEquals([
                'actions' => [
                    ['remove' => ['index' => $indexToDelete, 'alias' => '']],
                    ['add' => ['index' => $indexName, 'alias' => '']],
                ],
            ], json_decode($requests[1]->getBody()->getContents(), true));
        }

        if ($delete && !$exception) {
            $this->assertEquals('DELETE', $requests[2]->getMethod());
            $this->assertEquals(sprintf('/%s', $indexToDelete), $requests[2]->getUri()->getPath());
        }
    }

    /**
     * @return mixed[]
     */
    public static function addAliasDataProvider(): array
    {
        return [
            'add alias and no delete' => [
                'hasAlias' => true,
                'exception' => false,
                'delete' => false,
            ],
            'add alias and delete' => [
                'hasAlias' => true,
                'exception' => false,
                'delete' => true,
            ],
            'add alias exception and delete' => [
                'hasAlias' => false,
                'exception' => true,
                'delete' => true,
            ],
            'add alias exception and no delete' => [
                'hasAlias' => false,
                'exception' => true,
                'delete' => false,
            ],
        ];
    }

    /**
     * @param mixed[] $body
     */
    private function mockResponse(int $code = SymfonyResponse::HTTP_OK, array $body = []): void
    {
        $this->httpClient->addResponse(new Response($code, [Elasticsearch::HEADER_CHECK => Elasticsearch::PRODUCT_NAME, 'Content-Type' => 'application/json'], json_encode($body)));
    }
}
