<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Filters;

interface FieldFilterInterface extends FilterableInterface
{
    public function getField(): string;

    public function setField(string $field): void;
}
