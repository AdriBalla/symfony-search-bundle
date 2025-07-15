<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Client;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\FieldInfo;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexDefinitionNotFoundException;
use Adriballa\SymfonySearchBundle\Services\Indexes\Managers\IndexMappingManagerInterface;

class IndexMappingClient implements IndexMappingClientInterface
{
    public function __construct(private IndexMappingManagerInterface $indexMappingManager) {}

    /**
     * @return FieldInfo[]
     * @throws IndexDefinitionNotFoundException
     */
    public function getFilterableFields(Index $index): array
    {
        return $this->indexMappingManager->getFilterableFields($index);
    }

    /**
     * @return FieldInfo[]
     * @throws IndexDefinitionNotFoundException
     */
    public function getSortableFields(Index $index): array
    {
        return $this->indexMappingManager->getSortableFields($index);
    }
}
