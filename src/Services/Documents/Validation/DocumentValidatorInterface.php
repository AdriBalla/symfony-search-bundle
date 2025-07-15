<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Documents\Validation;

use Adriballa\SymfonySearchBundle\Services\Documents\DTO\Document;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface DocumentValidatorInterface
{
    public function validate(Index $index, Document $document): ConstraintViolationListInterface;

    /**
     * @param Document[] $documents
     *
     * @return ConstraintViolationListInterface[]
     */
    public function mValidate(Index $index, array $documents): array;
}
