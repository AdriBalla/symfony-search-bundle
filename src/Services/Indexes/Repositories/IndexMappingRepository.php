<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Repositories;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\IndexMapping;
use Adriballa\SymfonySearchBundle\Services\Indexes\Transformers\IndexMappingTransformer;

class IndexMappingRepository implements IndexMappingRepositoryInterface
{
    public function __construct(
        private IndexDefinitionRepository $indexDefinitionRepository,
        private IndexMappingTransformer $transformer,
    ) {}

    public function getIndexMapping(string $indexType): ?IndexMapping
    {
        $indexDefinition = $this->indexDefinitionRepository->getIndexDefinition($indexType);
        $indexMapping = $indexDefinition->getIndexMapping();

        $config = $this->transformer->getElasticsearchConfiguration($indexMapping);

        return new IndexMapping($indexType, $config);
    }
}
