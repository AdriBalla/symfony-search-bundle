<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Documents\Manager;

use Adriballa\SymfonySearchBundle\Services\Documents\DTO\Document;
use Adriballa\SymfonySearchBundle\Services\Documents\DTO\IndexationResponse;
use Adriballa\SymfonySearchBundle\Services\Documents\DTO\MultipleIndexationResponse;
use Adriballa\SymfonySearchBundle\Services\Documents\Manager\DocumentManager;
use Adriballa\SymfonySearchBundle\Services\Documents\Transformer\DocumentTransformer;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Managers\IndexNameManagerInterface;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Mock\Client;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

#[CoversClass(DocumentManager::class)]
class DocumentManagerTest extends TestCase
{
    private DocumentTransformer&MockObject $documentTransformer;

    private Client $httpClient;
    private LoggerInterface&MockObject $logger;
    private DocumentManager $documentManager;

    private IndexNameManagerInterface&MockObject $indexNameManager;

    protected function setUp(): void
    {
        $this->indexNameManager = $this->createMock(IndexNameManagerInterface::class);

        $this->documentTransformer = $this->createMock(DocumentTransformer::class);

        $this->httpClient = new Client();

        $client = ClientBuilder::create()
            ->setHttpClient($this->httpClient)
            ->build()
        ;

        $this->logger = $this->createMock(LoggerInterface::class);

        $this->documentManager = new DocumentManager(
            $this->documentTransformer,
            $client,
            $this->indexNameManager,
            $this->logger,
        );
    }

    /**
     * @dataProvider bulkOperationsDataProvider
     */
    public function testBulkOperation(string $method, string $transformerMethod, bool $error): void
    {
        $this->mockClientMultipleResponse($error, [1, 2]);

        $index = $this->createMock(Index::class);
        $body = ['this is the body'];
        $expectedBody = json_encode($body)."\n";
        $documents
            = [
                $this->createMock(Document::class),
                $this->createMock(Document::class),
            ];

        $this->documentTransformer->expects($this->once())
            ->method($transformerMethod)
            ->with($index, $documents)
            ->willReturnCallback(fn () => yield $body)
        ;

        $response = $this->documentManager->{$method}($index, $documents);

        $lastClientRequest = $this->httpClient->getLastRequest();
        $this->assertEquals('/_bulk', $lastClientRequest->getUri()->getPath());
        $this->assertEquals($expectedBody, $lastClientRequest->getBody()->getContents());

        if ($error) {
            $expectedResponse = new MultipleIndexationResponse(
                count($documents),
                count($documents),
                [1 => 'an error occurred', 2 => 'an error occurred'],
            );
        } else {
            $expectedResponse = new MultipleIndexationResponse(
                count($documents),
            );
        }

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @return mixed[]
     */
    public static function bulkOperationsDataProvider(): array
    {
        return [
            'mIndex without error' => [
                'method' => 'mIndex',
                'transformerMethod' => 'generateIndexInstructions',
                'error' => false,
            ],
            'mIndex with error' => [
                'method' => 'mIndex',
                'transformerMethod' => 'generateIndexInstructions',
                'error' => true,
            ],
            'mUpdate without error' => [
                'method' => 'mUpdate',
                'transformerMethod' => 'generatePartialUpdateInstructions',
                'error' => false,
            ],
            'mUpdate with error' => [
                'method' => 'mUpdate',
                'transformerMethod' => 'generatePartialUpdateInstructions',
                'error' => true,
            ],
        ];
    }

    /**
     * @dataProvider  singleOperationDataProvider
     */
    public function testSingleOperation(string $method, string $transformerMethod, bool $error): void
    {
        $this->mockClientMultipleResponse($error, [1]);

        $index = $this->createMock(Index::class);
        $body = ['this is the body'];
        $expectedBody = json_encode($body)."\n";
        $document = $this->createMock(Document::class);

        $this->documentTransformer->expects($this->once())
            ->method($transformerMethod)
            ->with($index, [$document])
            ->willReturnCallback(fn () => yield $body)
        ;

        $response = $this->documentManager->{$method}($index, $document);

        $lastClientRequest = $this->httpClient->getLastRequest();
        $this->assertEquals('/_bulk', $lastClientRequest->getUri()->getPath());
        $this->assertEquals($expectedBody, $lastClientRequest->getBody()->getContents());

        if ($error) {
            $expectedResponse = new IndexationResponse(
                false,
                [1 => 'an error occurred'],
            );
        } else {
            $expectedResponse = new IndexationResponse(
                true,
            );
        }

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @return mixed[]
     */
    public static function singleOperationDataProvider(): array
    {
        return [
            'mIndex without error' => [
                'method' => 'index',
                'transformerMethod' => 'generateIndexInstructions',
                'error' => false,
            ],
            'mIndex with error' => [
                'method' => 'index',
                'transformerMethod' => 'generateIndexInstructions',
                'error' => true,
            ],
            'mUpdate without error' => [
                'method' => 'update',
                'transformerMethod' => 'generatePartialUpdateInstructions',
                'error' => false,
            ],
            'mUpdate with error' => [
                'method' => 'update',
                'transformerMethod' => 'generatePartialUpdateInstructions',
                'error' => true,
            ],
        ];
    }

    /**
     * @return mixed[]
     */
    public static function getDataProvider(): array
    {
        return [
            'get document found' => [
                'exception' => 'index',
                'transformerMethod' => 'generateIndexInstructions',
                'error' => false,
            ],
            'mIndex with error' => [
                'method' => 'index',
                'transformerMethod' => 'generateIndexInstructions',
                'error' => true,
            ],
            'mUpdate without error' => [
                'method' => 'update',
                'transformerMethod' => 'generatePartialUpdateInstructions',
                'error' => false,
            ],
            'mUpdate with error' => [
                'method' => 'update',
                'transformerMethod' => 'generatePartialUpdateInstructions',
                'error' => true,
            ],
        ];
    }

    /**
     * @dataProvider getDocumentDataProvider
     *
     * @param null|mixed[] $source
     */
    public function testGetDocumentFound(string $id, ?array $source, ?ClientResponseException $exception, ?Document $expectedDocument): void
    {
        $index = new Index('test_type');
        $aliasName = 'test_alias';

        $body = [
            '_source' => $source,
        ];

        $this->indexNameManager->expects($this->once())
            ->method('getIndexName')
            ->with($index)
            ->willReturn('test_alias')
        ;

        if ($exception) {
            $this->httpClient->addResponse(
                new Response(
                    SymfonyResponse::HTTP_BAD_REQUEST,
                    [Elasticsearch::HEADER_CHECK => Elasticsearch::PRODUCT_NAME, 'Content-Type' => 'application/json'],
                    json_encode($body),
                ),
            );
        } else {
            $this->httpClient->addResponse(
                new Response(
                    SymfonyResponse::HTTP_OK,
                    [Elasticsearch::HEADER_CHECK => Elasticsearch::PRODUCT_NAME, 'Content-Type' => 'application/json'],
                    json_encode($body),
                ),
            );
        }

        $document = $this->documentManager->find($index, $id);

        if (!$exception) {
            $lastClientRequest = $this->httpClient->getLastRequest();
            $this->assertEquals(sprintf('/%s/_doc/%s', $aliasName, $id), $lastClientRequest->getUri()->getPath());
        }

        $this->assertEquals($expectedDocument, $document);
    }

    /**
     * @return mixed[]
     */
    public static function getDocumentDataProvider(): array
    {
        return [
            'successful document' => [
                'id' => 'test_id',
                'source' => [
                    'id' => 'test_id',
                    'doc' => 'this is the source',
                ],
                'exception' => null,
                'expectedDocument' => new Document(
                    'test_id',
                    [
                        'id' => 'test_id',
                        'doc' => 'this is the source',
                    ],
                ),
            ],
            'missing document' => [
                'id' => 'test_missing',
                'source' => null,
                'exception' => new ClientResponseException('error'),
                'expectedDocument' => null,
            ],
        ];
    }

    /**
     * @dataProvider deleteDocumentDataProvider
     */
    public function testDelete(string $id, ?ClientResponseException $exception, bool $expected): void
    {
        $index = new Index('test_type');
        $aliasName = 'test_alias';

        $this->indexNameManager->expects($this->once())
            ->method('getIndexName')
            ->with($index)
            ->willReturn($aliasName)
        ;

        if ($exception) {
            $this->httpClient->addResponse(
                new Response(
                    SymfonyResponse::HTTP_BAD_REQUEST,
                    [Elasticsearch::HEADER_CHECK => Elasticsearch::PRODUCT_NAME, 'Content-Type' => 'application/json'],
                ),
            );
        } else {
            $this->httpClient->addResponse(
                new Response(
                    SymfonyResponse::HTTP_OK,
                    [Elasticsearch::HEADER_CHECK => Elasticsearch::PRODUCT_NAME, 'Content-Type' => 'application/json'],
                ),
            );
        }

        $delete = $this->documentManager->delete($index, $id);

        if (!$exception) {
            $lastClientRequest = $this->httpClient->getLastRequest();
            $this->assertEquals(sprintf('/%s/_doc/%s', $aliasName, $id), $lastClientRequest->getUri()->getPath());
        }

        $this->assertEquals($expected, $delete);
    }

    /**
     * @return mixed[]
     */
    public static function deleteDocumentDataProvider(): array
    {
        return [
            'successful document' => [
                'id' => 'test_id',
                'exception' => null,
                'expected' => true,
            ],
            'missing document' => [
                'id' => 'test_missing',
                'exception' => new ClientResponseException('error'),
                'expected' => false,
            ],
        ];
    }

    /**
     * @param int[] $ids
     */
    private function mockClientMultipleResponse(bool $error, array $ids): void
    {
        $items = [];
        foreach ($ids as $id) {
            $items[] = [
                'update' => [
                    'status' => SymfonyResponse::HTTP_BAD_REQUEST,
                    '_id' => $id,
                    'error' => [
                        'reason' => 'an error occurred',
                    ],
                ],
            ];
        }

        $body = [
            'errors' => $error,
            'items' => $items,
        ];

        $this->httpClient->addResponse(
            new Response(
                SymfonyResponse::HTTP_OK,
                [Elasticsearch::HEADER_CHECK => Elasticsearch::PRODUCT_NAME, 'Content-Type' => 'application/json'],
                json_encode($body),
            ),
        );
    }
}
