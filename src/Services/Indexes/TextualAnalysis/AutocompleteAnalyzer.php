<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\TextualAnalysis;

class AutocompleteAnalyzer implements TextualAnalysisInterface
{
    public const ANALYZER_NAME = 'autocomplete';
    public const SEARCH_NAME = 'autocomplete_search';

    /**
     * @return mixed[]
     */
    public function getAnalyzer(): array
    {
        return [
            'tokenizer' => 'autocomplete',
            'filter' => [
                'lowercase',
                'asciifolding',
            ],
        ];
    }

    public function getAnalyzerName(): string
    {
        return self::ANALYZER_NAME;
    }

    /**
     * @return mixed[]
     */
    public function getFilters(): array
    {
        return [];
    }

    /**
     * @return mixed[]
     */
    public function getTokenizers(): array
    {
        return [
            'autocomplete' => [
                'type' => 'edge_ngram',
                'min_gram' => 2,
                'max_gram' => 20,
                'token_chars' => [
                    'letter',
                ],
            ],
        ];
    }

    /**
     * @return null|string[]
     */
    public function getSearchAnalyzer(): ?array
    {
        return [
            'tokenizer' => 'lowercase',
        ];
    }

    public function getSearchAnalyzerName(): ?string
    {
        return self::SEARCH_NAME;
    }
}
