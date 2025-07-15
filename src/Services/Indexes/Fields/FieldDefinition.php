<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Fields;

use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;
use Adriballa\SymfonySearchBundle\Services\Indexes\TextualAnalysis\TextualAnalysisInterface;

abstract class FieldDefinition implements FieldDefinitionInterface
{
    /**
     * @param TextualAnalysisInterface[] $textualAnalysis
     */
    public function __construct(
        private readonly string $path,
        private readonly FieldType $type,
        private readonly FieldScope $scope,
        private readonly ?SearchOptions $searchOptions = null,
        private readonly bool $sortable = false,
        private readonly array $textualAnalysis = [],
    ) {}

    public function getPath(): string
    {
        return $this->path;
    }

    public function getType(): FieldType
    {
        return $this->type;
    }

    public function getScope(): FieldScope
    {
        return $this->scope;
    }

    public function getSearchOptions(): ?SearchOptions
    {
        return $this->searchOptions;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    /**
     * @return TextualAnalysisInterface[]
     */
    public function getTextualAnalysis(): array
    {
        return $this->textualAnalysis;
    }

    /**
     * @return mixed[]
     */
    public function getElasticsearchConfiguration(): array
    {
        $esConfig = [
            'type' => $this->type->value,
        ];

        if (null === $this->getSearchOptions() && !$this->isSortable()) {
            $esConfig['index'] = false;
        }

        return $esConfig;
    }
}
