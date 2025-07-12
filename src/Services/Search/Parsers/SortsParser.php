<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Parsers;

use Adriballa\SymfonySearchBundle\Services\Search\Enums\SortDirection;
use Adriballa\SymfonySearchBundle\Services\Search\Exceptions\SortParsingException;
use Adriballa\SymfonySearchBundle\Services\Search\Sort\Sort;

class SortsParser extends Parser
{
    /**
     * @param  mixed[] $querySort
     * @return Sort[]
     */
    public function parse(array $querySort): array
    {
        $sorts = [];
        foreach ($querySort as $value) {
            $matches = [];

            if (preg_match('/^\s*([a-zA-Z_][a-zA-Z0-9_\.]*)\s+(ASC|DESC)\s*$/i', $this->clean($value), $matches)) {
                $sorts[] = new Sort($this->clean($matches[1]), SortDirection::from(strtolower($this->clean($matches[2]))));

                continue;
            }

            throw new SortParsingException($value);
        }

        return $sorts;
    }
}
