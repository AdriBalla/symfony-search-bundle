<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Parsers;

abstract class Parser
{
    protected function clean(string $input): string
    {
        return trim(trim($input, '"'));
    }
}
