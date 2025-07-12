<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Documents\Client;

use Adriballa\SymfonySearchBundle\Services\Documents\DTO\Document;
use Adriballa\SymfonySearchBundle\Services\Documents\DTO\IndexationResponse;
use Adriballa\SymfonySearchBundle\Services\Documents\DTO\MultipleIndexationResponse;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;

interface DocumentClientInterface
{
    public function getDocument(Index $index, string $id): ?Document;

    public function deleteDocument(Index $index, string $id): bool;

    public function indexDocument(Index $index, Document $document): IndexationResponse;

    public function updateDocument(Index $index, Document $document, bool $upsert = false): IndexationResponse;

    /**
     * @param Document[] $documents
     */
    public function mIndexDocuments(Index $index, array $documents): MultipleIndexationResponse;

    /**
     * @param Document[] $documents
     */
    public function mUpdateDocuments(Index $index, array $documents, bool $upsert = false): MultipleIndexationResponse;
}
