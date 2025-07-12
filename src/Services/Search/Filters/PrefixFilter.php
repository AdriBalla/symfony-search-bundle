<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Filters;

class PrefixFilter implements FieldFilterInterface
{
    public function __construct(
        private string $field,
        private string $value,
        private bool $caseInsensitive = true,
    ) {}

    public function getField(): string
    {
        return $this->field;
    }

    public function setField(string $field): void
    {
        $this->field = $field;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isCaseInsensitive(): bool
    {
        return $this->caseInsensitive;
    }

    /**
     * @return mixed[]
     */
    public function toElasticsearchFilter(): array
    {
        return [
            'prefix' => [
                $this->getField() => [
                    'value' => $this->getValue(),
                    'case_insensitive' => $this->isCaseInsensitive(),
                ],
            ],
        ];
    }
}
