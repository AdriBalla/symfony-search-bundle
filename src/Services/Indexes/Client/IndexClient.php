<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Client;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexDefinitionNotFoundException;
use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexNotFoundException;
use Adriballa\SymfonySearchBundle\Services\Indexes\Managers\IndexManagerInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Managers\IndexNameManagerInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Repositories\IndexDefinitionRepositoryInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Repositories\IndexMappingRepositoryInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Validation\IndexMappingValidatorInterface;

class IndexClient implements IndexClientInterface
{
    public function __construct(
        private readonly IndexManagerInterface $indexManager,
        private readonly IndexNameManagerInterface $indexNameManager,
        private readonly IndexDefinitionRepositoryInterface $indexDefinitionRepository,
        private readonly IndexMappingRepositoryInterface $indexMappingRepository,
        private readonly IndexMappingValidatorInterface $indexMappingValidator,
    ) {}

    public function indexExists(Index $index): bool
    {
        return $this->indexManager->indexExists($index);
    }

    public function createIndex(string $indexType, bool $addAlias = false, bool $deleteExisting = false): Index
    {
        $indexDefinition = $this->indexDefinitionRepository->getIndexDefinition($indexType);

        $this->indexMappingValidator->validate($indexDefinition->getIndexMapping());

        $mapping = $this->indexMappingRepository->getIndexMapping($indexType);

        $index = $this->indexManager->createIndex($mapping);

        if ($addAlias) {
            $this->indexManager->addAliasOnIndex($index, $deleteExisting);
        }

        return $index;
    }

    public function copyIndex(Index $index, bool $addAlias, bool $deleteExisting): Index
    {
        $source = new Index($index->getType(), $this->indexNameManager->getAliasName($index->getType()));

        if (!$this->indexExists($source)) {
            throw new IndexNotFoundException($index);
        }

        $destination = $this->createIndex($index->getType());

        $this->indexManager->copyIndex($source->getName(), $destination->getName());
        $this->indexManager->refreshIndex($destination);

        if ($addAlias) {
            $this->indexManager->addAliasOnIndex($destination, $deleteExisting);
        }

        return $destination;
    }

    public function deleteIndex(string $indexType): void
    {
        if (false === $this->indexDefinitionRepository->indexDefinitionExists($indexType)) {
            throw new IndexDefinitionNotFoundException($indexType);
        }

        $this->indexManager->deleteIndex(new Index($indexType));
    }
}
