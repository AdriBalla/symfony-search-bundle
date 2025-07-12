<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Sanitizers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Text\SearchableTextField;
use Adriballa\SymfonySearchBundle\Services\Search\Sort\Sort;

class SortsSanitizer
{
    public function __construct() {}

    /**
     * @param  Sort[]                     $sorts
     * @param  FieldDefinitionInterface[] $fieldDefinitions
     * @return Sort[]
     */
    public function sanitize(array $sorts, array $fieldDefinitions): array
    {
        $sanitizedSorts = [];

        foreach ($sorts as $sort) {
            if (in_array($sort->getField(), $this->getAuthorizedSorts())) {
                $sanitizedSorts[] = $sort;

                continue;
            }

            $field = $fieldDefinitions[$sort->getField()] ?? null;

            if (null === $field || !$field->isSortable()) {
                continue;
            }

            if ($field instanceof SearchableTextField) {
                $sort->setField($sort->getField().'.keyword');
            }

            $sanitizedSorts[] = $sort;
        }

        return $sanitizedSorts;
    }

    /**
     * @return string[]
     */
    private function getAuthorizedSorts(): array
    {
        return [
            '_score',
            '_script',
        ];
    }
}
