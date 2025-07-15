<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Documents\Client;

use Adriballa\SymfonySearchBundle\Services\Documents\DTO\Document;
use Adriballa\SymfonySearchBundle\Services\Documents\DTO\IndexationResponse;
use Adriballa\SymfonySearchBundle\Services\Documents\DTO\MultipleIndexationResponse;
use Adriballa\SymfonySearchBundle\Services\Documents\Manager\DocumentManagerInterface;
use Adriballa\SymfonySearchBundle\Services\Documents\Validation\DocumentValidatorInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Client\IndexClientInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexNotFoundException;

class DocumentClient implements DocumentClientInterface
{
    public function __construct(
        private IndexClientInterface $indexClient,
        private DocumentManagerInterface $documentManager,
        private DocumentValidatorInterface $documentValidator,
    ) {}

    public function indexDocument(Index $index, Document $document): IndexationResponse
    {
        $this->validateIndex($index);

        if ($validationResponse = $this->validateDocument($index, $document)) {
            return $validationResponse;
        }

        return $this->documentManager->index($index, $document);
    }

    public function updateDocument(Index $index, Document $document, bool $upsert = false): IndexationResponse
    {
        $this->validateIndex($index);

        if ($validationResponse = $this->validateDocument($index, $document)) {
            return $validationResponse;
        }

        return $this->documentManager->update($index, $document);
    }

    /**
     * @param Document[] $documents
     */
    public function mIndexDocuments(Index $index, array $documents): MultipleIndexationResponse
    {
        $this->validateIndex($index);

        $validationResponse = $this->validateDocuments($index, $documents);

        if (0 === count($documents)) {
            return $validationResponse;
        }

        $indexResponse = $this->documentManager->mIndex($index, $documents);

        return new MultipleIndexationResponse(
            $validationResponse->total,
            count($validationResponse->errors) + $indexResponse->failure,
            $validationResponse->errors + $indexResponse->errors,
        );
    }

    /**
     * @param Document[] $documents
     */
    public function mUpdateDocuments(Index $index, array $documents, bool $upsert = false): MultipleIndexationResponse
    {
        $this->validateIndex($index);

        $validationResponse = $this->validateDocuments($index, $documents);

        if (0 === count($documents)) {
            return $validationResponse;
        }

        $updateResponse = $this->documentManager->mUpdate($index, $documents);

        return new MultipleIndexationResponse(
            $validationResponse->total,
            count($validationResponse->errors) + $updateResponse->failure,
            $validationResponse->errors + $updateResponse->errors,
        );
    }

    public function getDocument(Index $index, string $id): ?Document
    {
        $this->validateIndex($index);

        return $this->documentManager->find($index, $id);
    }

    public function deleteDocument(Index $index, string $id): bool
    {
        $this->validateIndex($index);

        return $this->documentManager->delete($index, $id);
    }

    private function validateIndex(Index $index): void
    {
        if (!$this->indexClient->indexExists($index)) {
            throw new IndexNotFoundException($index);
        }
    }

    private function validateDocument(Index $index, Document $document): ?IndexationResponse
    {
        $violations = $this->documentValidator->validate($index, $document);

        if (0 === $violations->count()) {
            return null;
        }

        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return new IndexationResponse(false, $errors);
    }

    /**
     * @param Document[] $documents
     * @param-out array<string, Document> $documents
     * @return MultipleIndexationResponse
     */
    private function validateDocuments(Index $index, array &$documents): MultipleIndexationResponse
    {
        $total = count($documents);

        /** @var array<string, Document> $documentsById */
        $documentsById = array_reduce(
            $documents,
            fn (array $carry, Document $doc) => $carry + [$doc->getId() => $doc],
            [],
        );

        $violations = $this->documentValidator->mValidate($index, $documents);

        $errors = [];
        foreach ($violations as $documentId => $violationList) {
            if ($violationList->count() > 0) {
                unset($documentsById[$documentId]);
                foreach ($violationList as $violation) {
                    $errors[$documentId][$violation->getPropertyPath()] = $violation->getMessage();
                }
            }
        }

        $documents = $documentsById;

        return new MultipleIndexationResponse(
            $total,
            count($errors),
            $errors,
        );
    }
}
