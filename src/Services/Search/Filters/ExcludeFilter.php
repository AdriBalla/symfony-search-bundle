<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Filters;

class ExcludeFilter implements FilterableInterface
{
    public function __construct(
        private FilterableInterface $filter,
    ) {}

    public function getFilter(): FilterableInterface
    {
        return $this->filter;
    }

    public function toElasticsearchFilter(): array
    {
        return [
            'bool' => [
                'must_not' => [
                    $this->filter->toElasticsearchFilter(),
                ],
            ],
        ];
    }
}
