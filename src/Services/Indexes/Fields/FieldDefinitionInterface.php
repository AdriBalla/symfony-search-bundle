<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Fields;

use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;
use Adriballa\SymfonySearchBundle\Services\Indexes\TextualAnalysis\TextualAnalysisInterface;

interface FieldDefinitionInterface
{
    public function getPath(): string;

    public function getType(): FieldType;

    public function getScope(): FieldScope;

    public function isSortable(): bool;

    public function getSearchOptions(): ?SearchOptions;

    /**
     * @return TextualAnalysisInterface[]
     */
    public function getTextualAnalysis(): array;

    /**
     * @return mixed[]
     */
    public function getElasticsearchConfiguration(): array;
}
