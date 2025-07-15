<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Documents\Validation;

use Adriballa\SymfonySearchBundle\Services\Documents\DTO\Document;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Repositories\IndexDefinitionRepository;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DocumentValidator implements DocumentValidatorInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly IndexDefinitionRepository $indexDefinitionRepository,
        private readonly DocumentConstraintsGeneratorInterface $documentConstraintsGenerator,
    ) {}

    public function validate(Index $index, Document $document): ConstraintViolationListInterface
    {
        $indexDefinition = $this->indexDefinitionRepository->getIndexDefinition($index->getType());

        $errors = $this->validator->validate($document);

        $constraints = $this->documentConstraintsGenerator->getConstraints($indexDefinition->getIndexMapping());

        $errors->addAll($this->validator->validate($document->getBody(), $constraints));

        return $errors;
    }

    public function mValidate(Index $index, array $documents): array
    {
        $indexDefinition = $this->indexDefinitionRepository->getIndexDefinition($index->getType());
        $constraints = $this->documentConstraintsGenerator->getConstraints($indexDefinition->getIndexMapping());

        $violations = [];
        foreach ($documents as $document) {
            $errors = $this->validator->validate($document);
            $errors->addAll($this->validator->validate($document->getBody(), $constraints));

            $violations[$document->getId()] = $errors;
        }

        return $violations;
    }
}
