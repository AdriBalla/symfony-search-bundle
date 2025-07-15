<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Factories;

use Adriballa\SymfonySearchBundle\Controller\Search\Request\SearchIndexRequest;
use Adriballa\SymfonySearchBundle\Services\Search\SearchRequest;

interface SearchRequestFactoryInterface
{
    public function create(string $indexType, SearchIndexRequest $searchIndexRequest): SearchRequest;
}
