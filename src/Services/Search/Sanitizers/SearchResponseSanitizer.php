<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Sanitizers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\MultiPropertiesDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Repositories\IndexDefinitionRepositoryInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScopeServiceInterface;
use Adriballa\SymfonySearchBundle\Services\Search\SearchResponse;

class SearchResponseSanitizer implements SearchResponseSanitizerInterface
{
    public function __construct(
        private readonly IndexDefinitionRepositoryInterface $indexDefinitionRepository,
        private readonly FieldScopeServiceInterface $fieldScopeService,
    ) {}

    public function sanitize(SearchResponse $searchResponse): SearchResponse
    {
        $indexDefinition = $this->indexDefinitionRepository->getIndexDefinition($searchResponse->indexType);

        $sanitizedHits = [];
        foreach ($searchResponse->hits as $hit) {
            $sanitizedHits[] = $this->sanitizeHit($hit, $indexDefinition->getIndexMapping()->getFields());
        }

        $searchResponse->hits = $sanitizedHits;

        return $searchResponse;
    }

    /**
     * @param  mixed[]                    $hit
     * @param  FieldDefinitionInterface[] $fields
     * @return mixed[]
     */
    private function sanitizeHit(array $hit, array $fields): array
    {
        $sanitizedHit = [];
        foreach ($fields as $field) {
            if (isset($hit[$field->getPath()]) && $this->fieldScopeService->isAccessible($field->getScope())) {
                if ($field instanceof MultiPropertiesDefinitionInterface) {
                    $sanitizedHit[$field->getPath()] = $this->sanitizeHit($hit[$field->getPath()], $field->getProperties());
                } else {
                    $sanitizedHit[$field->getPath()] = $hit[$field->getPath()];
                }
            }
        }

        return $sanitizedHit;
    }
}
