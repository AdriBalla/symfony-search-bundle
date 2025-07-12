<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\TextualAnalysis;

class SimpleAnalyzer implements TextualAnalysisInterface
{
    public const ANALYZER_NAME = 'simple_analyzer';

    /**
     * @return mixed[]
     */
    public function getAnalyzer(): array
    {
        return [
            'type' => 'custom',
            'tokenizer' => 'lowercase',
            'filter' => [
                'classic',
                'asciifolding',
                'unique',
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

    public function getTokenizers(): array
    {
        return [];
    }

    /**
     * @return null|mixed[]
     */
    public function getSearchAnalyzer(): ?array
    {
        return null;
    }

    public function getSearchAnalyzerName(): ?string
    {
        return null;
    }
}
