<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Filters;

class MatchFilter implements FieldFilterInterface
{
    public function __construct(
        private string $field,
        private string $value,
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

    /**
     * @return mixed[]
     */
    public function toElasticsearchFilter(): array
    {
        return [
            'match' => [
                $this->getField() => [
                    'query' => $this->getValue(),
                    'operator' => 'and',
                ],
            ],
        ];
    }
}
