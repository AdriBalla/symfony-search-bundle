<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Sanitizers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScopeServiceInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\FieldFilterInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\FilterableInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\MultipleFiltersInterface;

class FiltersSanitizer
{
    public function __construct(private FieldScopeServiceInterface $fieldScopeService) {}

    /**
     * @param  FilterableInterface[]      $filters
     * @param  FieldDefinitionInterface[] $fieldDefinitions
     * @return FilterableInterface[]
     */
    public function sanitize(array $filters, array $fieldDefinitions): array
    {
        $sanitizedFilters = [];

        foreach ($filters as $filter) {
            if ($filter instanceof MultipleFiltersInterface) {
                $filter->setFilters($this->sanitize($filter->getFilters(), $fieldDefinitions));
            }

            if ($filter instanceof FieldFilterInterface) {
                if (!$this->isAccessible($filter, $fieldDefinitions)) {
                    continue;
                }

                if (FieldType::SearchableText == $fieldDefinitions[$filter->getField()]->getType()) {
                    $filter->setField($filter->getField().'.keyword');
                }
            }

            $sanitizedFilters[] = $filter;
        }

        return $sanitizedFilters;
    }

    /**
     * @param FieldDefinitionInterface[] $fieldDefinitions
     */
    private function isAccessible(FieldFilterInterface $filter, array $fieldDefinitions): bool
    {
        return isset($fieldDefinitions[$filter->getField()]) && $this->fieldScopeService->isAccessible($fieldDefinitions[$filter->getField()]->getScope());
    }
}
