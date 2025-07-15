<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Repositories;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\IndexMapping;

interface IndexMappingRepositoryInterface
{
    public function getIndexMapping(string $indexType): ?IndexMapping;
}
