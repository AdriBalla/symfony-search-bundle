<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Controllers\Indexes;

use Adriballa\SymfonySearchBundle\Controller\Indexes\IndexesController;
use Adriballa\SymfonySearchBundle\Controller\Indexes\Request\CreateIndexRequest;
use Adriballa\SymfonySearchBundle\Controller\Indexes\Request\DeleteIndexRequest;
use Adriballa\SymfonySearchBundle\Services\Indexes\Client\IndexClientInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Client\IndexMappingClientInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexDefinitionNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[CoversClass(IndexesController::class)]
class IndexesControllerTest extends TestCase
{
    private MockObject&ValidatorInterface $validator;

    private MockObject&SerializerInterface $serializer;

    private IndexClientInterface&MockObject $indexClient;

    private IndexMappingClientInterface&MockObject $indexMappingClient;

    private IndexesController $controller;

    public function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->indexClient = $this->createMock(IndexClientInterface::class);
        $this->indexMappingClient = $this->createMock(IndexMappingClientInterface::class);

        $this->controller = new IndexesController();
    }

    /**
     * @dataProvider provideCreateIndexSuccessCases
     */
    public function testAddIndexSuccess(bool $addAlias, bool $deleteExisting): void
    {
        $index = new Index('test_index', 'test_index_name');
        $requestData = json_encode([
            'indexType' => 'my_index',
            'addAlias' => $addAlias,
            'deleteExisting' => $deleteExisting,
        ]);

        $request = new Request([], [], [], [], [], [], $requestData);

        $createIndexRequest = new CreateIndexRequest();
        $createIndexRequest->indexType = 'my_index';
        $createIndexRequest->addAlias = $addAlias;
        $createIndexRequest->deleteExisting = $deleteExisting;

        $this->serializer->method('deserialize')
            ->willReturn($createIndexRequest)
        ;

        $this->validator->method('validate')
            ->willReturn(new ConstraintViolationList())
        ;

        $this->indexClient->expects($this->once())
            ->method('createIndex')
            ->with('my_index', $addAlias, $deleteExisting)
            ->willReturn($index)
        ;

        $response = $this->controller->addIndex($request, $this->serializer, $this->validator, $this->indexClient);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode($index), $response->getContent());
    }

    /**
     * @return mixed[]
     */
    public static function provideCreateIndexSuccessCases(): array
    {
        return [
            'addAlias and delete existing' => [
                'addAlias' => true,
                'deleteExisting' => true,
            ],
            'addAlias and not delete existing' => [
                'addAlias' => true,
                'deleteExisting' => false,
            ],
            'no addAlias and no delete existing' => [
                'addAlias' => false,
                'deleteExisting' => false,
            ],
            'no addAlias and delete existing' => [
                'addAlias' => false,
                'deleteExisting' => true,
            ],
        ];
    }

    public function testAddIndexValidationError(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode([]));

        $this->serializer->method('deserialize')
            ->willReturn(new CreateIndexRequest())
        ;

        $violation = new ConstraintViolation('This value should not be blank.', null, [], '', 'indexType', '');
        $this->validator->method('validate')
            ->willReturn(new ConstraintViolationList([$violation]))
        ;

        $response = $this->controller->addIndex($request, $this->serializer, $this->validator, $this->indexClient);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['errors' => ['indexType' => 'This value should not be blank.']]), $response->getContent());
    }

    public function testAddIndexException(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode([]));

        $this->serializer->method('deserialize')
            ->willThrowException(new \Exception('Unexpected error'))
        ;

        $response = $this->controller->addIndex($request, $this->serializer, $this->validator, $this->indexClient);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['error' => 'Unexpected error']), $response->getContent());
    }

    public function testRemoveIndexSuccess(): void
    {
        $requestData = json_encode(['indexType' => 'my_index']);
        $request = new Request([], [], [], [], [], [], $requestData);

        $deleteIndexRequest = new DeleteIndexRequest();
        $deleteIndexRequest->indexType = 'my_index';

        $this->serializer->method('deserialize')
            ->willReturn($deleteIndexRequest)
        ;

        $this->validator->method('validate')
            ->willReturn(new ConstraintViolationList())
        ;

        $this->indexClient->expects($this->once())
            ->method('deleteIndex')
            ->with('my_index')
        ;

        $response = $this->controller->removeIndex($request, $this->indexClient, $this->serializer, $this->validator);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testRemoveIndexValidationError(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode([]));

        $this->serializer->method('deserialize')
            ->willReturn(new DeleteIndexRequest())
        ;

        $violation = new ConstraintViolation('This value should not be blank.', null, [], '', 'indexType', '');

        $this->validator->method('validate')
            ->willReturn(new ConstraintViolationList([$violation]))
        ;

        $response = $this->controller->removeIndex($request, $this->indexClient, $this->serializer, $this->validator);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['errors' => ['indexType' => 'This value should not be blank.']]), $response->getContent());
    }

    public function testRemoveIndexException(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode([]));

        $this->serializer->method('deserialize')
            ->willThrowException(new \Exception('Unexpected error'))
        ;

        $controller = new IndexesController();
        $response = $controller->removeIndex($request, $this->indexClient, $this->serializer, $this->validator);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['error' => 'Unexpected error']), $response->getContent());
    }

    public function testGetFilterablesFieldsSuccess(): void
    {
        $client = $this->createMock(IndexMappingClientInterface::class);
        $client->method('getFilterableFields')
            ->willReturn(['field1', 'field2'])
        ;

        $controller = new IndexesController();
        $response = $controller->getFilterablesFields('my_index', $client);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['field1', 'field2']), $response->getContent());
    }

    public function testGetFilterablesFieldsNotFound(): void
    {
        $this->indexMappingClient->method('getFilterableFields')
            ->willThrowException(new IndexDefinitionNotFoundException('my_index'))
        ;

        $response = $this->controller->getFilterablesFields('my_index', $this->indexMappingClient);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testGetSortablesFieldsSuccess(): void
    {
        $this->indexMappingClient->method('getSortableFields')
            ->willReturn(['field1', 'field2'])
        ;

        $response = $this->controller->getSortablesFields('my_index', $this->indexMappingClient);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['field1', 'field2']), $response->getContent());
    }

    public function testGetSortablesFieldsNotFound(): void
    {
        $this->indexMappingClient->method('getSortableFields')
            ->willThrowException(new IndexDefinitionNotFoundException('my_index'))
        ;

        $response = $this->controller->getSortablesFields('my_index', $this->indexMappingClient);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
