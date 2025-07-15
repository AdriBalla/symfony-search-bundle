<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Documents\Client;

use Adriballa\SymfonySearchBundle\Services\Documents\Client\DocumentClient;
use Adriballa\SymfonySearchBundle\Services\Documents\DTO\Document;
use Adriballa\SymfonySearchBundle\Services\Documents\DTO\IndexationResponse;
use Adriballa\SymfonySearchBundle\Services\Documents\DTO\MultipleIndexationResponse;
use Adriballa\SymfonySearchBundle\Services\Documents\Manager\DocumentManagerInterface;
use Adriballa\SymfonySearchBundle\Services\Documents\Validation\DocumentValidatorInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Client\IndexClientInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

#[CoversClass(DocumentClient::class)]
class DocumentClientTest extends TestCase
{
    private IndexClientInterface&MockObject $indexClient;

    private DocumentManagerInterface&MockObject $documentManager;

    private DocumentValidatorInterface&MockObject $documentValidator;

    private DocumentClient $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->indexClient = $this->createMock(IndexClientInterface::class);
        $this->documentManager = $this->createMock(DocumentManagerInterface::class);
        $this->documentValidator = $this->createMock(DocumentValidatorInterface::class);

        $this->client = new DocumentClient($this->indexClient, $this->documentManager, $this->documentValidator);
    }

    /**
     * @dataProvider  singleOperationDataProvider
     */
    public function testIndexDocument(bool $indexExists, ?ConstraintViolationList $violations, ?string $exception, ?IndexationResponse $expectedResponse): void
    {
        $index = $this->createMock(Index::class);
        $document = $this->createMock(Document::class);
        $indexationResponse = new IndexationResponse(true, []);

        $this->indexClient->expects($this->once())
            ->method('indexExists')
            ->with($index)
            ->willReturn($indexExists)
        ;

        if ($indexExists && $violations) {
            $this->documentValidator->expects($this->once())
                ->method('validate')
                ->with($index, $document)
                ->willReturn($violations)
            ;
        }

        if ($exception) {
            $this->expectException($exception);
        }

        if (!$exception && !$violations) {
            $this->documentManager->expects($this->once())
                ->method('index')
                ->with($index, $document)
                ->willReturn($indexationResponse)
            ;
        }

        $this->assertEquals($expectedResponse, $this->client->indexDocument($index, $document));
    }

    /**
     * @dataProvider  singleOperationDataProvider
     */
    public function testUpdateDocument(bool $indexExists, ?ConstraintViolationList $violations, ?string $exception, ?IndexationResponse $expectedResponse): void
    {
        $index = $this->createMock(Index::class);
        $document = $this->createMock(Document::class);
        $indexationResponse = new IndexationResponse(true, []);

        $this->indexClient->expects($this->once())
            ->method('indexExists')
            ->with($index)
            ->willReturn($indexExists)
        ;

        if ($indexExists && $violations) {
            $this->documentValidator->expects($this->once())
                ->method('validate')
                ->with($index, $document)
                ->willReturn($violations)
            ;
        }

        if ($exception) {
            $this->expectException($exception);
        }

        if (!$exception && !$violations) {
            $this->documentManager->expects($this->once())
                ->method('update')
                ->with($index, $document)
                ->willReturn($indexationResponse)
            ;
        }

        $this->assertEquals($expectedResponse, $this->client->updateDocument($index, $document));
    }

    /**
     * @return mixed[]
     */
    public static function singleOperationDataProvider(): array
    {
        return [
            'index exists' => [
                'indexExists' => true,
                'violations' => null,
                'exception' => null,
                'expectedResponse' => new IndexationResponse(true, []),
            ],
            'index does not exists' => [
                'indexExists' => false,
                'violations' => null,
                'exception' => IndexNotFoundException::class,
                'expectedResponse' => null,
            ],
            'index exists with violations' => [
                'indexExists' => true,
                'violations' => new ConstraintViolationList([
                    new ConstraintViolation(
                        message: 'field is missing',
                        messageTemplate: 'field is missing',
                        parameters: [],
                        root: 'test',
                        propertyPath: 'test',
                        invalidValue: 'test',
                    ),
                ]),
                'exception' => null,
                'expectedResponse' => new IndexationResponse(false, [
                    'test' => 'field is missing',
                ]),
            ],
        ];
    }

    /**
     * @dataProvider  multipleOperationDataProvider
     * @param ConstraintViolationList[] $violations
     */
    public function testMIndexDocument(bool $indexExists, ?array $violations, ?string $exception, ?MultipleIndexationResponse $expectedResponse): void
    {
        $index = $this->createMock(Index::class);
        $document1 = $this->createMock(Document::class);
        $document2 = $this->createMock(Document::class);

        $document1->method('getId')->willReturn('1');
        $document2->method('getId')->willReturn('2');

        $documents = [$document1, $document2];

        $multipleIndexationResponse = new MultipleIndexationResponse(2, 0);

        $this->indexClient->expects($this->once())
            ->method('indexExists')
            ->with($index)
            ->willReturn($indexExists)
        ;

        if ($indexExists && $violations) {
            $this->documentValidator->expects($this->once())
                ->method('mValidate')
                ->with($index, $documents)
                ->willReturn($violations)
            ;
        }

        $expectedDocuments = [
            '1' => $document1,
            '2' => $document2,
        ];

        if ($violations) {
            foreach (array_keys($violations) as $violation) {
                unset($expectedDocuments[$violation]);
            }
        }

        if ($exception) {
            $this->expectException($exception);
        }

        if (!$exception && !empty($expectedDocuments)) {
            $this->documentManager->expects($this->once())
                ->method('mIndex')
                ->with($index, $expectedDocuments)
                ->willReturn($multipleIndexationResponse)
            ;
        }
        $this->assertEquals($expectedResponse, $this->client->mIndexDocuments($index, $documents));
    }

    /**
     * @dataProvider  multipleOperationDataProvider
     * @param ConstraintViolationList[] $violations
     */
    public function testMUpdateDocument(bool $indexExists, ?array $violations, ?string $exception, ?MultipleIndexationResponse $expectedResponse): void
    {
        $index = $this->createMock(Index::class);
        $document1 = $this->createMock(Document::class);
        $document2 = $this->createMock(Document::class);

        $document1->method('getId')->willReturn('1');
        $document2->method('getId')->willReturn('2');

        $documents = [$document1, $document2];

        $multipleIndexationResponse = new MultipleIndexationResponse(2, 0);

        $this->indexClient->expects($this->once())
            ->method('indexExists')
            ->with($index)
            ->willReturn($indexExists)
        ;

        if ($indexExists && $violations) {
            $this->documentValidator->expects($this->once())
                ->method('mValidate')
                ->with($index, $documents)
                ->willReturn($violations)
            ;
        }

        $expectedDocuments = [
            '1' => $document1,
            '2' => $document2,
        ];

        if ($violations) {
            foreach (array_keys($violations) as $violation) {
                unset($expectedDocuments[$violation]);
            }
        }

        if ($exception) {
            $this->expectException($exception);
        }

        if (!$exception && !empty($expectedDocuments)) {
            $this->documentManager->expects($this->once())
                ->method('mUpdate')
                ->with($index, $expectedDocuments)
                ->willReturn($multipleIndexationResponse)
            ;
        }
        $this->assertEquals($expectedResponse, $this->client->mUpdateDocuments($index, $documents));
    }

    /**
     * @return mixed[]
     */
    public static function multipleOperationDataProvider(): array
    {
        return [
            'index exists' => [
                'indexExists' => true,
                'violations' => null,
                'exception' => null,
                'expectedResponse' => new MultipleIndexationResponse(2, 0),
            ],
            'index does not exists' => [
                'indexExists' => false,
                'violations' => null,
                'exception' => IndexNotFoundException::class,
                'expectedResponse' => null,
            ],
            'index exists with one violation' => [
                'indexExists' => true,
                'violations' => [
                    '1' => new ConstraintViolationList([
                        new ConstraintViolation(
                            message: 'field is missing',
                            messageTemplate: 'field is missing',
                            parameters: [],
                            root: 'test',
                            propertyPath: 'test',
                            invalidValue: 'test',
                        ),
                    ]),
                ],
                'exception' => null,
                'expectedResponse' => new MultipleIndexationResponse(
                    2,
                    1,
                    [
                        '1' => ['test' => 'field is missing'],
                    ],
                ),
            ],
            'index exists with 2 violations' => [
                'indexExists' => true,
                'violations' => [
                    '1' => new ConstraintViolationList([
                        new ConstraintViolation(
                            message: 'field is missing',
                            messageTemplate: 'field is missing',
                            parameters: [],
                            root: 'test_1',
                            propertyPath: 'test_1',
                            invalidValue: 'test_1',
                        ),
                    ]),
                    '2' => new ConstraintViolationList([
                        new ConstraintViolation(
                            message: 'field is missing',
                            messageTemplate: 'field is missing',
                            parameters: [],
                            root: 'test_2',
                            propertyPath: 'test_2',
                            invalidValue: 'test_2',
                        ),
                    ]),
                ],
                'exception' => null,
                'expectedResponse' => new MultipleIndexationResponse(
                    2,
                    2,
                    [
                        '1' => ['test_1' => 'field is missing'],
                        '2' => ['test_2' => 'field is missing'],
                    ],
                ),
            ],
        ];
    }

    /**
     * @dataProvider  documentOperationDataProvider
     */
    public function testGetDocument(bool $indexExists, ?string $exception): void
    {
        $id = 'test_id';
        $index = $this->createMock(Index::class);
        $document = $this->createMock(Document::class);

        $this->indexClient->expects($this->once())
            ->method('indexExists')
            ->with($index)
            ->willReturn($indexExists)
        ;

        if ($exception) {
            $this->expectException($exception);
        } else {
            $this->documentManager->expects($this->once())
                ->method('find')
                ->with($index, $id)
                ->willReturn($document)
            ;
        }

        $this->assertEquals($document, $this->client->getDocument($index, $id));
    }

    /**
     * @dataProvider  documentOperationDataProvider
     */
    public function testDeleteDocument(bool $indexExists, ?string $exception): void
    {
        $id = 'test_id';
        $index = $this->createMock(Index::class);

        $this->indexClient->expects($this->once())
            ->method('indexExists')
            ->with($index)
            ->willReturn($indexExists)
        ;

        if ($exception) {
            $this->expectException($exception);
        } else {
            $this->documentManager->expects($this->once())
                ->method('delete')
                ->with($index, $id)
                ->willReturn(true)
            ;
        }

        $this->assertTrue($this->client->deleteDocument($index, $id));
    }

    /**
     * @return mixed[]
     */
    public static function documentOperationDataProvider(): array
    {
        return [
            'index exists' => [
                'indexExists' => true,
                'exception' => null,
            ],
            'index does not exists' => [
                'indexExists' => false,
                'exception' => IndexNotFoundException::class,
            ],
        ];
    }
}
