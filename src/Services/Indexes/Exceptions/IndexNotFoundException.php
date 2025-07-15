<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;

class IndexNotFoundException extends \Exception
{
    public function __construct(Index $index)
    {
        $message = sprintf('Index for type %s not found', $index->getType());
        if (null !== $index->getName()) {
            $message = sprintf('Index for type %s and name %s not found', $index->getType(), $index->getName());
        }

        parent::__construct($message);
    }
}
