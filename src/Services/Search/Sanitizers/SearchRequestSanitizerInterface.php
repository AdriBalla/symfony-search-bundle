<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Sanitizers;

use Adriballa\SymfonySearchBundle\Services\Search\PendingSearchRequest;
use Adriballa\SymfonySearchBundle\Services\Search\SearchRequest;

interface SearchRequestSanitizerInterface
{
    public function sanitize(SearchRequest $searchRequest): PendingSearchRequest;
}
