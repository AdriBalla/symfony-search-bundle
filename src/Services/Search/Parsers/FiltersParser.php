<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Parsers;

use Adriballa\SymfonySearchBundle\Services\Search\Filters\ExactMatchFilter;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\FilterableInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\RangeFilter;

class FiltersParser extends Parser
{
    /**
     * @param  mixed[]               $queryFilters
     * @return FilterableInterface[]
     */
    public function parse(array $queryFilters): array
    {
        $filters = [];
        foreach ($queryFilters as $field => $queryFilter) {
            $field = $this->clean($field);

            // Test for ranges
            $ranges = explode('..', $queryFilter);
            if (count($ranges) > 1) {
                $from = !empty($ranges[0]) ? $ranges[0] : null;
                $to = !empty($ranges[1]) ? $ranges[1] : null;
                $filters[] = new RangeFilter($field, $from, $to);

                continue;
            }

            // Get all string filters
            $values = [];
            preg_match_all('/(["\'])(.*?)(\1)/', $queryFilter, $values);
            if (!empty($values[2])) {
                $filters[] = new ExactMatchFilter($field, array_map(fn ($v) => $this->clean($v), $values[2]));

                continue;
            }

            // Get all numeric filters
            $ranges = explode(',', $queryFilter);
            $filters[] = new ExactMatchFilter($field, array_map(fn ($v) => $this->clean($v), $ranges));
        }

        return $filters;
    }
}
