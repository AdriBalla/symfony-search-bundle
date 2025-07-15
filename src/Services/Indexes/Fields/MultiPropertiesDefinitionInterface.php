<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Fields;

interface MultiPropertiesDefinitionInterface extends FieldDefinitionInterface
{
    /**
     * @return FieldDefinitionInterface[]
     */
    public function getProperties(): array;
}
