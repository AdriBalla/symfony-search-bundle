<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Controller\Indexes\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CreateIndexRequest
{
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    public string $indexType;
    #[Assert\Type(type: 'bool')]
    public bool $addAlias = true;
    #[Assert\Type(type: 'bool')]
    public bool $deleteExisting = false;
}
