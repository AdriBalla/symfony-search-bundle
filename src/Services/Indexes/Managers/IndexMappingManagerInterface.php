<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Managers;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\FieldInfo;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexDefinitionNotFoundException;

interface IndexMappingManagerInterface
{
    /**
     * @return FieldInfo[]
     * @throws IndexDefinitionNotFoundException
     */
    public function getFilterableFields(Index $index): array;

    /**
     * @return FieldInfo[]
     * @throws IndexDefinitionNotFoundException
     */
    public function getSortableFields(Index $index): array;
}
