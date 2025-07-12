<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Filters;

class ExistFilter implements FieldFilterInterface
{
    public function __construct(private string $field) {}

    public function getField(): string
    {
        return $this->field;
    }

    public function setField(string $field): void
    {
        $this->field = $field;
    }

    public function toElasticsearchFilter(): array
    {
        return [
            'exists' => [
                'field' => $this->field,
            ],
        ];
    }
}
