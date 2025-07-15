<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Exceptions;

class SortParsingException extends \Exception
{
    public function __construct(string $input)
    {
        parent::__construct(sprintf('Could not parse sort %s".', $input));
    }
}
