<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;

class PrivateIndexException extends \Exception
{
    public function __construct(Index $index)
    {
        $message = sprintf('Index for type %s is private', $index->getType());
        if (null !== $index->getName()) {
            $message = sprintf('Index for type %s and name %s is private', $index->getType(), $index->getName());
        }

        parent::__construct($message);
    }
}
