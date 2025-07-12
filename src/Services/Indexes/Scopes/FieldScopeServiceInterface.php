<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Scopes;

interface FieldScopeServiceInterface
{
    public function isAccessible(FieldScope $scope): bool;
}
