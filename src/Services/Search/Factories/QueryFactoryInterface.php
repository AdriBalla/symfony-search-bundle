<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Factories;

use Adriballa\SymfonySearchBundle\Services\Search\PendingSearchRequest;

interface QueryFactoryInterface
{
    /**
     * @return mixed[]
     */
    public function getQueryFromRequest(PendingSearchRequest $searchRequest): array;
}
