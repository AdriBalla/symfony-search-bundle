<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Fields;

interface MultiFieldsDefinitionInterface extends FieldDefinitionInterface
{
    /**
     * @return FieldDefinitionInterface[]
     */
    public function getFields(): array;
}
