<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions;

class IndexDefinitionNotFoundException extends \Exception
{
    public function __construct(string $indexType)
    {
        parent::__construct(sprintf('Index definition for type %s not found', $indexType));
    }
}
