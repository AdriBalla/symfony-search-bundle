<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Documents\Manager;

use Adriballa\SymfonySearchBundle\Services\Documents\DTO\Document;
use Adriballa\SymfonySearchBundle\Services\Documents\DTO\IndexationResponse;
use Adriballa\SymfonySearchBundle\Services\Documents\DTO\MultipleIndexationResponse;
use Adriballa\SymfonySearchBundle\Services\Documents\Transformer\DocumentTransformer;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Managers\IndexNameManagerInterface;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Response;

class DocumentManager implements DocumentManagerInterface
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly DocumentTransformer $documentTransformer,
        private readonly Client $client,
        private readonly IndexNameManagerInterface $indexNameManager,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function index(Index $index, Document $document): IndexationResponse
    {
        $mResponse = $this->mIndex($index, [$document]);

        return new IndexationResponse(0 == $mResponse->failure, $mResponse->errors);
    }

    public function update(Index $index, Document $document, bool $upsert = false): IndexationResponse
    {
        $mResponse = $this->mUpdate($index, [$document]);

        return new IndexationResponse(0 == $mResponse->failure, $mResponse->errors);
    }

    /**
     * @param Document[] $documents
     */
    public function mIndex(Index $index, array $documents): MultipleIndexationResponse
    {
        $results = $this->client->bulk([
            'body' => $this->documentTransformer->generateIndexInstructions($index, $documents),
        ]);

        return $this->fillResponse($results->asArray());
    }

    /**
     * @param Document[] $documents
     */
    public function mUpdate(Index $index, array $documents, bool $upsert = false): MultipleIndexationResponse
    {
        $results = $this->client->bulk([
            'body' => $this->documentTransformer->generatePartialUpdateInstructions($index, $documents, $upsert),
        ]);

        return $this->fillResponse($results->asArray());
    }

    public function find(Index $index, string $id): ?Document
    {
        try {
            $response = $this->client->get([
                'index' => $this->indexNameManager->getIndexName($index),
                'id' => $id,
            ])->asArray()['_source'];

            return new Document(
                (string) $response['id'],
                $response,
            );
        } catch (ClientResponseException) {
            $this->logger->info("No document for type {$index->getType()} with id {$id}.");
        }

        return null;
    }

    public function delete(Index $index, string $id): bool
    {
        try {
            $this->client->delete([
                'index' => $this->indexNameManager->getIndexName($index),
                'id' => $id,
            ]);
        } catch (ClientResponseException) {
            $this->logger->info("No document for type {$index->getType()} with id {$id} to delete.");

            return false;
        }

        return true;
    }

    /**
     * @param mixed[] $results
     */
    private function fillResponse(array $results): MultipleIndexationResponse
    {
        $total = count($results['items']);
        $errors = [];
        $failure = 0;

        if (!$results['errors']) {
            return new MultipleIndexationResponse($total);
        }

        foreach ($results['items'] as $result) {
            if (Response::HTTP_BAD_REQUEST === ($result['update']['status'] ?? null)) {
                ++$failure;

                $errors[$result['update']['_id']] = $result['update']['error']['reason'];

                $this->logger->error("Error indexing document: {$result['update']['error']['reason']}", [
                    'document' => $result['update']['_id'],
                ]);
            }
        }

        return new MultipleIndexationResponse($total, $failure, $errors);
    }
}
