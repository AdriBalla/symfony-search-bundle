<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\TextualAnalysis;

class StandardAnalyzer implements TextualAnalysisInterface
{
    public const ANALYZER_NAME = 'standard_analyzer';

    /**
     * @return mixed[]
     */
    public function getAnalyzer(): array
    {
        return [
            'type' => 'standard',
            'tokenizer' => 'standard',
            'filter' => [
                'lowercase',
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
