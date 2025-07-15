<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Documents\Validation;

use Adriballa\SymfonySearchBundle\Services\Documents\DTO\Document;
use Adriballa\SymfonySearchBundle\Services\Documents\Validation\DocumentConstraintsGeneratorInterface;
use Adriballa\SymfonySearchBundle\Services\Documents\Validation\DocumentValidator;
use Adriballa\SymfonySearchBundle\Services\Documents\Validation\DocumentValidatorInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Definition\IndexDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Repositories\IndexDefinitionRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[CoversClass(DocumentValidator::class)]
class DocumentValidatorTest extends TestCase
{
    private MockObject&ValidatorInterface $validator;
    private IndexDefinitionRepository&MockObject $indexDefinitionRepository;
    private DocumentConstraintsGeneratorInterface&MockObject $documentConstraintsGenerator;

    private DocumentValidatorInterface $documentValidator;

    public function setUp(): void
    {
        $this->documentConstraintsGenerator = $this->createMock(DocumentConstraintsGeneratorInterface::class);
        $this->indexDefinitionRepository = $this->createMock(IndexDefinitionRepository::class);
        $this->validator = $this->createMock(ValidatorInterface::class);

        $this->documentValidator = new DocumentValidator(
            $this->validator,
            $this->indexDefinitionRepository,
            $this->documentConstraintsGenerator,
        );
    }

    public function testValidate(): void
    {
        $indexType = 'test_index';
        $body = ['body of the document'];

        $index = $this->createMock(Index::class);
        $document = $this->createMock(Document::class);
        $indexDefinition = $this->createMock(IndexDefinitionInterface::class);
        $indexMapping = $this->createMock(IndexMappingInterface::class);
        $constraints = $this->createMock(Constraints\Collection::class);
        $documentViolations = $this->createMock(ConstraintViolationListInterface::class);
        $violations = $this->createMock(ConstraintViolationListInterface::class);

        $document->expects($this->once())->method('getBody')->willReturn($body);

        $index->expects($this->once())->method('getType')->willReturn($indexType);
        $indexDefinition->expects($this->once())->method('getIndexMapping')->willReturn($indexMapping);

        $this->indexDefinitionRepository->expects($this->once())
            ->method('getIndexDefinition')
            ->with($indexType)
            ->willReturn($indexDefinition)
        ;

        $this->documentConstraintsGenerator->expects($this->once())
            ->method('getConstraints')
            ->with($indexMapping)
            ->willReturn($constraints)
        ;

        $this->validator->expects($this->exactly(2))
            ->method('validate')
            ->willReturnCallback(function ($input, $c) use ($body, $constraints, $document, $documentViolations, $violations) {
                if ($input == $body && $c == $constraints) {
                    return $violations;
                }

                if ($input == $document && null == $c) {
                    return $documentViolations;
                }

                $this->fail('unexpected parameter in validate');
            })
        ;

        $documentViolations->expects($this->once())
            ->method('addAll')
            ->with($violations)
        ;

        $this->assertEquals($documentViolations, $this->documentValidator->validate($index, $document));
    }

    public function testMValidate(): void
    {
        $indexType = 'test_index';
        $body1 = ['body of the document 1'];
        $body2 = ['body of the document 2'];

        $index = $this->createMock(Index::class);
        $document1 = $this->createMock(Document::class);
        $document2 = $this->createMock(Document::class);
        $indexDefinition = $this->createMock(IndexDefinitionInterface::class);
        $indexMapping = $this->createMock(IndexMappingInterface::class);
        $constraints = $this->createMock(Constraints\Collection::class);
        $violations1 = $this->createMock(ConstraintViolationListInterface::class);
        $violations2 = $this->createMock(ConstraintViolationListInterface::class);

        $documentViolations1 = $this->createMock(ConstraintViolationListInterface::class);
        $documentViolations2 = $this->createMock(ConstraintViolationListInterface::class);

        $document1->expects($this->once())->method('getBody')->willReturn($body1);
        $document1->expects($this->once())->method('getId')->willReturn('1');

        $document2->expects($this->once())->method('getBody')->willReturn($body2);
        $document2->expects($this->once())->method('getId')->willReturn('2');

        $index->expects($this->once())->method('getType')->willReturn($indexType);
        $indexDefinition->expects($this->once())->method('getIndexMapping')->willReturn($indexMapping);

        $this->indexDefinitionRepository->expects($this->once())
            ->method('getIndexDefinition')
            ->with($indexType)
            ->willReturn($indexDefinition)
        ;

        $this->documentConstraintsGenerator->expects($this->once())
            ->method('getConstraints')
            ->with($indexMapping)
            ->willReturn($constraints)
        ;

        $this->validator->expects($this->exactly(4))
            ->method('validate')
            ->willReturnCallback(function ($input, $c) use (
                $body1,
                $body2,
                $document1,
                $document2,
                $violations1,
                $violations2,
                $documentViolations1,
                $documentViolations2
            ) {
                if ($input == $body1) {
                    return $violations1;
                }

                if ($input == $body2) {
                    return $violations2;
                }

                if ($input == $document1 && null == $c) {
                    return $documentViolations1;
                }

                if ($input == $document2 && null == $c) {
                    return $documentViolations2;
                }

                $this->fail('unexpected parameter in validate');
            })
        ;

        $documentViolations1->expects($this->once())
            ->method('addAll')
            ->with($violations1)
        ;

        $documentViolations2->expects($this->once())
            ->method('addAll')
            ->with($violations2)
        ;

        $expected = [
            '1' => $violations1,
            '2' => $violations2,
        ];

        $this->assertEquals($expected, $this->documentValidator->mValidate($index, [$document1, $document2]));
    }
}
