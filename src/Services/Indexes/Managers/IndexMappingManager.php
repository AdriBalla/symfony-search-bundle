<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Managers;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\FieldInfo;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexDefinitionNotFoundException;
use Adriballa\SymfonySearchBundle\Services\Indexes\Repositories\IndexDefinitionRepositoryInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Resolvers\IndexMappingFieldsResolver;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScopeServiceInterface;

class IndexMappingManager implements IndexMappingManagerInterface
{
    public function __construct(
        private readonly IndexDefinitionRepositoryInterface $indexDefinitionRepository,
        private readonly IndexMappingFieldsResolver $indexMappingFieldsResolver,
        private readonly FieldScopeServiceInterface $fieldScopeService,
    ) {}

    /**
     * @return FieldInfo[]
     *
     * @throws IndexDefinitionNotFoundException
     */
    public function getFilterableFields(Index $index): array
    {
        $indexDefinition = $this->indexDefinitionRepository->getIndexDefinition($index->getType());

        $fieldDefinitions = $this->indexMappingFieldsResolver->resolve($indexDefinition->getIndexMapping());
        $filters = [];

        foreach ($fieldDefinitions as $path => $fieldDefinition) {
            if (null !== $fieldDefinition->getSearchOptions() && $this->fieldScopeService->isAccessible($fieldDefinition->getScope())) {
                $filters[] = new FieldInfo($path, $fieldDefinition->getType());
            }
        }

        return $filters;
    }

    /**
     * @return FieldInfo[]
     *
     * @throws IndexDefinitionNotFoundException
     */
    public function getSortableFields(Index $index): array
    {
        $indexDefinition = $this->indexDefinitionRepository->getIndexDefinition($index->getType());

        $fieldDefinitions = $this->indexMappingFieldsResolver->resolve($indexDefinition->getIndexMapping());

        $sorts = [];

        foreach ($fieldDefinitions as $path => $fieldDefinition) {
            if ($fieldDefinition->isSortable() && $this->fieldScopeService->isAccessible($fieldDefinition->getScope())) {
                $sorts[] = new FieldInfo($path, $fieldDefinition->getType());
            }
        }

        return $sorts;
    }
}
