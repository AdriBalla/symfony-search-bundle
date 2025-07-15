<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Sanitizers;

use Adriballa\SymfonySearchBundle\Services\Search\SearchResponse;

interface SearchResponseSanitizerInterface
{
    public function sanitize(SearchResponse $searchResponse): SearchResponse;
}
