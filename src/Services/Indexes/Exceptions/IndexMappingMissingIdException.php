<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions;

class IndexMappingMissingIdException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Missing keyword field id in index mapping');
    }
}
