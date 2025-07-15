<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Factories;

use Adriballa\SymfonySearchBundle\Services\Search\PendingSearchRequest;

class DefaultQueryFactory implements QueryFactoryInterface
{
    /**
     * @return mixed[]
     */
    public function getQueryFromRequest(PendingSearchRequest $searchRequest): array
    {
        $esSearchFields = [];
        foreach ($searchRequest->getSearchedFields() as $searchField) {
            $esSearchFields[] = $searchField->getElasticsearchFieldString();
        }

        // Generate fulltext query
        $phraseQuery = [
            'multi_match' => [
                'fields' => $esSearchFields,
                'operator' => 'and',
                'query' => $searchRequest->getQueryString(),
                'boost' => 15,
                'type' => 'phrase',
            ],
        ];

        $queryBoosted = [
            'multi_match' => [
                'fields' => $esSearchFields,
                'operator' => 'and',
                'query' => $searchRequest->getQueryString(),
                'boost' => 2,
                'type' => 'best_fields',
            ],
        ];

        $queryExtended = [
            'multi_match' => [
                'fields' => $esSearchFields,
                'operator' => 'and',
                'query' => $searchRequest->getQueryString(),
                'fuzziness' => 'AUTO',
                'max_expansions' => 3,
                'boost' => 1,
                'type' => 'best_fields',
            ],
        ];

        return [
            'bool' => [
                'should' => [
                    $phraseQuery,
                    $queryBoosted,
                    $queryExtended,
                ],
                'minimum_should_match' => 1,
            ],
        ];
    }
}
