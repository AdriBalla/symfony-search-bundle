<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Scopes;

interface IndexScopeServiceInterface
{
    public function isAccessible(IndexScope $scope): bool;
}
