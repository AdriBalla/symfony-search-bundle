<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions;

class IndexMappingDuplicatesPathsException extends \Exception
{
    /**
     * @param string[] $duplicates
     */
    public function __construct(array $duplicates)
    {
        parent::__construct(sprintf('Duplicates paths %s in index mapping', implode(',', $duplicates)));
    }
}
