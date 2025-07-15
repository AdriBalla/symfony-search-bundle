<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Sort;

interface SortableInterface
{
    /**
     * @return mixed[]
     */
    public function toElasticsearchSort(): array;
}
