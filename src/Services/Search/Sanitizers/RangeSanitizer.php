<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Sanitizers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Definition\IndexDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Exceptions\SearchRangeLimitExceeded;
use Adriballa\SymfonySearchBundle\Services\Search\Range;

class RangeSanitizer
{
    public function sanitize(?Range $range, IndexDefinitionInterface $indexDefinition): Range
    {
        if (!$range) {
            return new Range();
        }

        if (!$range->getSize()) {
            return $range;
        }

        $endPosition = $range->getStart() + $range->getSize();

        if ($endPosition > $indexDefinition->getPaginationLimit()) {
            throw new SearchRangeLimitExceeded($endPosition, $indexDefinition->getPaginationLimit());
        }

        return $range;
    }
}
