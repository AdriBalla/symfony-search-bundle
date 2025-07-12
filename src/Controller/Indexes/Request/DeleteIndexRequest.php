<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Controller\Indexes\Request;

use Symfony\Component\Validator\Constraints as Assert;

class DeleteIndexRequest
{
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    public string $indexType;
}
