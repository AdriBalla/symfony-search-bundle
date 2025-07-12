<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Filters;

class ExactMatchFilter implements FieldFilterInterface
{
    /**
     * @param mixed[] $values
     */
    public function __construct(
        private string $field,
        private array $values,
    ) {}

    public function getField(): string
    {
        return $this->field;
    }

    public function setField(string $field): void
    {
        $this->field = $field;
    }

    /**
     * @return mixed[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return mixed[]
     */
    public function toElasticsearchFilter(): array
    {
        return [
            'terms' => [
                $this->getField() => $this->getValues(),
            ],
        ];
    }
}
