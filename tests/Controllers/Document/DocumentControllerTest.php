<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Controllers\Document;

use Adriballa\SymfonySearchBundle\Controller\Documents\DocumentsController;
use Adriballa\SymfonySearchBundle\Services\Documents\Client\DocumentClientInterface;
use Adriballa\SymfonySearchBundle\Services\Documents\DTO\Document;
use Adriballa\SymfonySearchBundle\Services\Documents\DTO\IndexationResponse;
use Adriballa\SymfonySearchBundle\Services\Documents\DTO\MultipleIndexationResponse;
use Adriballa\SymfonySearchBundle\Services\Indexes\Client\IndexClientInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(DocumentsController::class)]
class DocumentControllerTest extends TestCase
{
    private DocumentClientInterface&MockObject $documentClient;
    private IndexClientInterface&MockObject $indexClient;
    private DocumentsController $controller;

    protected function setUp(): void
    {
        $this->documentClient = $this->createMock(DocumentClientInterface::class);
        $this->indexClient = $this->createMock(IndexClientInterface::class);
        $this->controller = new DocumentsController();
    }

    public function testAddDocumentReturnsNotFound(): void
    {
        $this->indexClient->method('indexExists')->willReturn(false);

        $request = new Request([], [], [], [], [], [], json_encode(['foo' => 'bar']));
        $response = $this->controller->addDocument('test_index', 'doc1', $request, $this->documentClient, $this->indexClient);

        $this->assertSame(JsonResponse::class, $response::class);
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testAddDocumentReturnsSuccess(): void
    {
        $indexationResponse = new IndexationResponse(true);
        $this->indexClient->method('indexExists')->willReturn(true);
        $this->documentClient->method('indexDocument')->willReturn($indexationResponse);

        $request = new Request([], [], [], [], [], [], json_encode(['foo' => 'bar']));
        $response = $this->controller->addDocument('test_index', 'doc1', $request, $this->documentClient, $this->indexClient);

        $this->assertSame(json_encode($indexationResponse), $response->getContent());
    }

    public function testMAddDocumentNotFound(): void
    {
        $this->indexClient->method('indexExists')->willReturn(false);

        $request = new Request([], [], [], [], [], [], json_encode([]));
        $response = $this->controller->mAddDocument('index', $request, $this->documentClient, $this->indexClient);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testMAddDocumentSuccess(): void
    {
        $indexationResponse = new MultipleIndexationResponse(1, 0, []);
        $this->indexClient->method('indexExists')->willReturn(true);
        $this->documentClient->method('mIndexDocuments')->willReturn($indexationResponse);

        $request = new Request([], [], [], [], [], [], json_encode([
            ['id' => 'doc1', 'foo' => 'bar'],
        ]));

        $response = $this->controller->mAddDocument('index', $request, $this->documentClient, $this->indexClient);
        $this->assertSame(json_encode($indexationResponse), $response->getContent());
    }

    public function testUpdateNotFound(): void
    {
        $this->indexClient->method('indexExists')->willReturn(false);

        $request = new Request([], [], [], [], [], [], json_encode(['foo' => 'bar']));
        $response = $this->controller->update('type', 'id', $request, $this->documentClient, $this->indexClient);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testUpdateSuccess(): void
    {
        $indexationResponse = new IndexationResponse(true);
        $this->indexClient->method('indexExists')->willReturn(true);
        $this->documentClient->method('updateDocument')->willReturn($indexationResponse);

        $request = new Request([], [], [], [], [], [], json_encode(['foo' => 'bar']));
        $response = $this->controller->update('type', 'id', $request, $this->documentClient, $this->indexClient);

        $this->assertEquals(json_encode($indexationResponse), $response->getContent());
    }

    public function testMUpdateNotFound(): void
    {
        $this->indexClient->method('indexExists')->willReturn(false);
        $request = new Request([], [], [], [], [], [], json_encode([]));

        $response = $this->controller->mUpdateDocument('type', $request, $this->documentClient, $this->indexClient);
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testMUpdateSuccess(): void
    {
        $indexationResponse = new MultipleIndexationResponse(1, 0, []);
        $this->indexClient->method('indexExists')->willReturn(true);
        $this->documentClient->method('mUpdateDocuments')->willReturn($indexationResponse);

        $request = new Request([], [], [], [], [], [], json_encode([
            ['id' => 'doc1', 'bar' => 'baz'],
        ]));

        $response = $this->controller->mUpdateDocument('type', $request, $this->documentClient, $this->indexClient);
        $this->assertEquals(json_encode($indexationResponse), $response->getContent());
    }

    public function testGetReturns404IfIndexMissing(): void
    {
        $this->indexClient->method('indexExists')->willReturn(false);
        $response = $this->controller->get('type', 'id', $this->documentClient, $this->indexClient);
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testGetReturns404IfDocumentNotFound(): void
    {
        $this->indexClient->method('indexExists')->willReturn(true);
        $this->documentClient->method('getDocument')->willReturn(null);

        $response = $this->controller->get('type', 'id', $this->documentClient, $this->indexClient);
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testGetReturnsDocumentBody(): void
    {
        $this->indexClient->method('indexExists')->willReturn(true);

        $mockDocument = $this->createMock(Document::class);
        $mockDocument->method('getBody')->willReturn(['foo' => 'bar']);

        $this->documentClient->method('getDocument')->willReturn($mockDocument);

        $response = $this->controller->get('type', 'id', $this->documentClient, $this->indexClient);
        $this->assertSame(['foo' => 'bar'], json_decode($response->getContent(), true));
    }

    public function testDeleteReturns404IfIndexMissing(): void
    {
        $this->indexClient->method('indexExists')->willReturn(false);
        $response = $this->controller->delete('type', 'id', $this->documentClient, $this->indexClient);
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testDeleteReturns404IfNothingToDelete(): void
    {
        $this->indexClient->method('indexExists')->willReturn(true);
        $this->documentClient->method('deleteDocument')->willReturn(false);

        $response = $this->controller->delete('type', 'id', $this->documentClient, $this->indexClient);
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testDeleteReturnsTrue(): void
    {
        $this->indexClient->method('indexExists')->willReturn(true);

        $this->documentClient->method('deleteDocument')->willReturn(true);

        $response = $this->controller->delete('type', 'id', $this->documentClient, $this->indexClient);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
