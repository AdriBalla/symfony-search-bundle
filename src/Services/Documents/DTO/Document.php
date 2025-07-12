<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Documents\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Document
{
    /**
     * @param mixed[] $body
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\NotNull]
        private ?string $id,
        #[Assert\NotNull]
        private array $body,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return mixed[]
     */
    public function getBody(): array
    {
        return $this->body;
    }
}
