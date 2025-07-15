<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Controller\Documents;

use Adriballa\SymfonySearchBundle\Services\Documents\Client\DocumentClientInterface;
use Adriballa\SymfonySearchBundle\Services\Documents\DTO\Document;
use Adriballa\SymfonySearchBundle\Services\Indexes\Client\IndexClientInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class DocumentsController extends AbstractController
{
    #[Route('/indexes/{indexType}/documents/{id}', name: 'document.add', methods: ['POST'])]
    public function addDocument(
        string $indexType,
        string $id,
        Request $request,
        DocumentClientInterface $documentClient,
        IndexClientInterface $indexClient,
    ): JsonResponse {
        $document = new Document($id, $request->toArray());
        $index = new Index($indexType);

        if (!$indexClient->indexExists($index)) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $response = $documentClient->indexDocument($index, $document);

        return new JsonResponse($response);
    }

    #[Route('/indexes/{indexType}/documents', name: 'document.add.bulk', methods: ['POST'])]
    public function mAddDocument(
        string $indexType,
        Request $request,
        DocumentClientInterface $documentClient,
        IndexClientInterface $indexClient,
    ): JsonResponse {
        $index = new Index($indexType);

        if (!$indexClient->indexExists($index)) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $documents = [];
        foreach ($request->toArray() as $document) {
            $documents[] = new Document((string) ($document['id'] ?? null), $document);
        }

        $response = $documentClient->mIndexDocuments($index, $documents);

        return new JsonResponse($response);
    }

    #[Route('/indexes/{indexType}/documents/{id}', name: 'document.update', methods: ['PUT', 'PATCH'])]
    public function update(
        string $indexType,
        string $id,
        Request $request,
        DocumentClientInterface $documentClient,
        IndexClientInterface $indexClient,
    ): JsonResponse {
        $document = new Document($id, $request->toArray());
        $index = new Index($indexType);

        if (!$indexClient->indexExists($index)) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $response = $documentClient->updateDocument($index, $document);

        return new JsonResponse($response);
    }

    #[Route('/indexes/{indexType}/documents', name: 'document.update.bulk', methods: ['PUT', 'PATCH'])]
    public function mUpdateDocument(
        string $indexType,
        Request $request,
        DocumentClientInterface $documentClient,
        IndexClientInterface $indexClient,
    ): JsonResponse {
        $index = new Index($indexType);

        if (!$indexClient->indexExists($index)) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $documents = [];
        foreach ($request->toArray() as $document) {
            $documents[] = new Document((string) ($document['id'] ?? null), $document);
        }

        $response = $documentClient->mUpdateDocuments($index, $documents);

        return new JsonResponse($response);
    }

    #[Route('/indexes/{indexType}/documents/{id}', name: 'document.get', methods: ['GET'])]
    public function get(
        string $indexType,
        string $id,
        DocumentClientInterface $documentClient,
        IndexClientInterface $indexClient,
    ): JsonResponse {
        $index = new Index($indexType);

        if (!$indexClient->indexExists($index)) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $document = $documentClient->getDocument($index, $id);

        if (!$document) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($document->getBody());
    }

    #[Route('/indexes/{indexType}/documents/{id}', name: 'document.delete', methods: ['DELETE'])]
    public function delete(
        string $indexType,
        string $id,
        DocumentClientInterface $documentClient,
        IndexClientInterface $indexClient,
    ): JsonResponse {
        $index = new Index($indexType);

        if (!$indexClient->indexExists($index)) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $delete = $documentClient->deleteDocument($index, $id);

        if (!$delete) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
