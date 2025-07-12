<?php

namespace Adriballa\SymfonySearchBundle\Services\Search\Sort;

use Adriballa\SymfonySearchBundle\Services\Search\Enums\SortDirection;

class Sort implements SortableInterface
{
    public function __construct(
        private string $field,
        private readonly SortDirection $direction,
    ) {}

    public function getField(): string
    {
        return $this->field;
    }

    public function getDirection(): SortDirection
    {
        return $this->direction;
    }

    /**
     * @return mixed[]
     */
    public function toElasticsearchSort(): array
    {
        return [
            $this->getField() => [
                'order' => $this->direction->value],
        ];
    }

    public function setField(string $field): void
    {
        $this->field = $field;
    }
}
