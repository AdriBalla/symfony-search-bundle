<?php

namespace Adriballa\SymfonySearchBundle\Services\Indexes\TextualAnalysis;

interface TextualAnalysisInterface
{
    /**
     * @return mixed[]
     */
    public function getFilters(): ?array;

    /**
     * @return mixed[]
     */
    public function getTokenizers(): ?array;

    /**
     * @return mixed[]
     */
    public function getAnalyzer(): ?array;

    public function getAnalyzerName(): ?string;

    /**
     * @return null|mixed[]
     */
    public function getSearchAnalyzer(): ?array;

    public function getSearchAnalyzerName(): ?string;
}
