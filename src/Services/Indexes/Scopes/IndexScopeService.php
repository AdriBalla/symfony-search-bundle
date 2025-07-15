<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Scopes;

class IndexScopeService implements IndexScopeServiceInterface
{
    public function isAccessible(IndexScope $scope): bool
    {
        return IndexScope::Private !== $scope;
    }
}
