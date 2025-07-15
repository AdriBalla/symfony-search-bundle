<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Documents\Transformer;

use Adriballa\SymfonySearchBundle\Services\Documents\DTO\Document;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Managers\IndexNameManagerInterface;

class DocumentTransformer
{
    public function __construct(
        private readonly IndexNameManagerInterface $indexNameManager,
    ) {}

    /**
     * @param  Document[] $documents
     * @return mixed[]
     */
    public function generateIndexInstructions(Index $index, array $documents): iterable
    {
        $indexName = $this->indexNameManager->getIndexName($index);

        foreach ($documents as $document) {
            yield [
                'index' => [
                    '_index' => $indexName,
                    '_id' => $document->getId(),
                ],
            ];

            yield array_merge($document->getBody(), ['id' => $document->getId()]);
        }
    }

    /**
     * @param  Document[] $documents
     * @return mixed[]
     */
    public function generatePartialUpdateInstructions(Index $index, array $documents, bool $docAsUpsert = false): iterable
    {
        $indexName = $this->indexNameManager->getIndexName($index);

        foreach ($documents as $document) {
            yield [
                'update' => [
                    '_index' => $indexName,
                    '_id' => $document->getId(),
                ],
            ];

            yield [
                'doc' => $document->getBody(),
                'doc_as_upsert' => $docAsUpsert,
            ];
        }
    }
}
