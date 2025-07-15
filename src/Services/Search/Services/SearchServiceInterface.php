<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Services;

use Adriballa\SymfonySearchBundle\Services\Search\PendingSearchRequest;
use Adriballa\SymfonySearchBundle\Services\Search\SearchResponse;

interface SearchServiceInterface
{
    public function search(PendingSearchRequest $pendingSearchRequest): SearchResponse;
}
