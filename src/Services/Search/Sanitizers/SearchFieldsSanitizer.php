<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Sanitizers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScopeServiceInterface;
use Adriballa\SymfonySearchBundle\Services\Search\SearchField;

class SearchFieldsSanitizer
{
    public function __construct(
        private readonly FieldScopeServiceInterface $fieldScopeService,
    ) {}

    /**
     * @param string[]                   $searchFields
     * @param FieldDefinitionInterface[] $fieldDefinitions
     *
     * @return SearchField[]
     */
    public function sanitize(array $searchFields, array $fieldDefinitions): array
    {
        $sanitizedFields = [];

        if (empty($searchFields)) {
            foreach ($fieldDefinitions as $path => $fieldDefinition) {
                if (null !== $fieldDefinition->getSearchOptions()
                    && FieldType::SearchableText == $fieldDefinition->getType()
                    && $this->fieldScopeService->isAccessible($fieldDefinition->getScope())) {
                    $sanitizedFields[] = new SearchField($path, $fieldDefinition->getSearchOptions()->getBoost());
                }
            }

            return $sanitizedFields;
        }

        foreach ($searchFields as $searchField) {
            if (
                !isset($fieldDefinitions[$searchField])
                || null === $fieldDefinitions[$searchField]->getSearchOptions()
                || !$this->fieldScopeService->isAccessible($fieldDefinitions[$searchField]->getScope())
            ) {
                continue;
            }

            $sanitizedFields[] = new SearchField($searchField, $fieldDefinitions[$searchField]->getSearchOptions()->getBoost());
        }

        return $sanitizedFields;
    }
}
