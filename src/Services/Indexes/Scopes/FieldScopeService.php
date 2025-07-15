<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Scopes;

class FieldScopeService implements FieldScopeServiceInterface
{
    public function isAccessible(FieldScope $scope): bool
    {
        return FieldScope::Private !== $scope;
    }
}
