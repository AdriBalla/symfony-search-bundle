<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Repositories;

use Adriballa\SymfonySearchBundle\Services\Indexes\Definition\IndexDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexDefinitionNotFoundException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class IndexDefinitionRepository implements IndexDefinitionRepositoryInterface
{
    /**
     * @var IndexDefinitionInterface[]
     */
    private iterable $indexDefinitions = [];

    /**
     * @param iterable<IndexDefinitionInterface> $indexDefinitions
     */
    public function __construct(
        #[AutowireIterator('search.index.definition')]
        iterable $indexDefinitions,
    ) {
        foreach ($indexDefinitions as $indexDefinition) {
            $this->indexDefinitions[$indexDefinition::getIndexType()] = $indexDefinition;
        }
    }

    public function getIndexDefinition(string $indexType): IndexDefinitionInterface
    {
        $indexDefinition = $this->indexDefinitions[$indexType] ?? null;

        if ($indexDefinition) {
            return $indexDefinition;
        }

        throw new IndexDefinitionNotFoundException($indexType);
    }

    public function indexDefinitionExists(string $indexType): bool
    {
        return isset($this->indexDefinitions[$indexType]);
    }
}
