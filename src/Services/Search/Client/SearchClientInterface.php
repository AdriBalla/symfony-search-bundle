<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Client;

use Adriballa\SymfonySearchBundle\Services\Search\SearchRequest;
use Adriballa\SymfonySearchBundle\Services\Search\SearchResponse;

interface SearchClientInterface
{
    public function search(SearchRequest $request): SearchResponse;
}
