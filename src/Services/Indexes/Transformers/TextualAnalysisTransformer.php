<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Transformers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Resolvers\IndexMappingFieldsResolver;

class TextualAnalysisTransformer
{
    public function __construct(private readonly IndexMappingFieldsResolver $indexMappingFieldsResolver) {}

    /**
     * @return mixed[]
     */
    public function transform(IndexMappingInterface $indexMapping): array
    {
        $flattenedFields = $this->indexMappingFieldsResolver->resolve($indexMapping);

        $filters = [];
        $tokenizers = [];
        $analyzers = [];
        foreach ($flattenedFields as $field) {
            if (null !== $field->getTextualAnalysis()) {
                $textualAnalysis = $field->getTextualAnalysis();
                foreach ($textualAnalysis as $entry) {
                    $filters = array_merge($filters, $entry->getFilters() ?? []);
                    $tokenizers = array_merge($tokenizers, $entry->getTokenizers() ?? []);

                    if (null !== $entry->getAnalyzer()) {
                        $analyzers[$entry->getAnalyzerName()] = $entry->getAnalyzer();
                    }

                    if (null !== $entry->getSearchAnalyzer()) {
                        $analyzers[$entry->getSearchAnalyzerName()] = $entry->getSearchAnalyzer();
                    }
                }
            }
        }

        return [
            'analysis' => [
                'filter' => $filters,
                'tokenizer' => $tokenizers,
                'analyzer' => $analyzers,
            ],
        ];
    }
}
