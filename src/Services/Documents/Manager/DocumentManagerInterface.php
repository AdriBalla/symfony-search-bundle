<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Documents\Manager;

use Adriballa\SymfonySearchBundle\Services\Documents\DTO\Document;
use Adriballa\SymfonySearchBundle\Services\Documents\DTO\IndexationResponse;
use Adriballa\SymfonySearchBundle\Services\Documents\DTO\MultipleIndexationResponse;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;

interface DocumentManagerInterface
{
    public function index(Index $index, Document $document): IndexationResponse;

    public function update(Index $index, Document $document, bool $upsert = false): IndexationResponse;

    /**
     * @param Document[] $documents
     */
    public function mIndex(Index $index, array $documents): MultipleIndexationResponse;

    /**
     * @param Document[] $documents
     */
    public function mUpdate(Index $index, array $documents, bool $upsert = false): MultipleIndexationResponse;

    public function find(Index $index, string $id): ?Document;

    public function delete(Index $index, string $id): bool;
}
