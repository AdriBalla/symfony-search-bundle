<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Exceptions;

class FilterParsingException extends \Exception
{
    public function __construct(string $field, string $value)
    {
        parent::__construct(sprintf('Filter parsing error for field "%s" and "%s".', $field, $value));
    }
}
