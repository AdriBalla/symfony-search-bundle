<?php

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Text;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\KeywordField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinition;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\MultiFieldsDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\SearchOptions;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;
use Adriballa\SymfonySearchBundle\Services\Indexes\TextualAnalysis\AutocompleteAnalyzer;
use Adriballa\SymfonySearchBundle\Services\Indexes\TextualAnalysis\SimpleAnalyzer;
use Adriballa\SymfonySearchBundle\Services\Indexes\TextualAnalysis\TextualAnalysisInterface;

class SearchableTextField extends FieldDefinition implements MultiFieldsDefinitionInterface
{
    /**
     * @param TextualAnalysisInterface[] $textualAnalysis
     */
    public function __construct(
        string $path,
        bool $autocomplete = false,
        int $boost = 1,
        FieldScope $scope = FieldScope::Public,
        array $textualAnalysis = [],
        bool $sortable = false,
    ) {
        $textualAnalyzer = false;
        foreach ($textualAnalysis as $analysis) {
            if ($analysis->getSearchAnalyzer() || $analysis->getAnalyzer()) {
                $textualAnalyzer = true;
            }
        }

        if (!$textualAnalyzer) {
            $textualAnalysis[] = new SimpleAnalyzer();
        }

        if ($autocomplete) {
            $textualAnalysis[] = new AutocompleteAnalyzer();
        }

        parent::__construct(
            path: $path,
            type: FieldType::SearchableText,
            scope: $scope,
            searchOptions: new SearchOptions($boost),
            sortable: $sortable,
            textualAnalysis: $textualAnalysis,
        );
    }

    public function getFields(): array
    {
        return [new KeywordField('keyword')];
    }

    public function getElasticsearchConfiguration(): array
    {
        $config = parent::getElasticsearchConfiguration();

        foreach ($this->getFields() as $subField) {
            $config['fields'][$subField->getPath()] = $subField->getElasticsearchConfiguration();
        }

        $textualAnalysis = $this->getTextualAnalysis();
        $defaultConfig = array_shift($textualAnalysis);

        if (null !== $defaultConfig) {
            $defaultConfig = $this->generateFieldFromTextualAnalysis($defaultConfig);

            $config = array_merge($config, $defaultConfig);

            foreach ($textualAnalysis as $analysis) {
                $config['fields'][$analysis->getAnalyzerName()] = array_merge($defaultConfig, $this->generateFieldFromTextualAnalysis($analysis));
            }
        }

        return $config;
    }

    /**
     * @return mixed[]
     */
    private function generateFieldFromTextualAnalysis(TextualAnalysisInterface $analysis): array
    {
        $config = [
            'type' => FieldType::SearchableText->value,
        ];

        if ($analysis->getSearchAnalyzer() || $analysis->getAnalyzer()) {
            $config['analyzer'] = $analysis->getAnalyzerName();
            $config['search_analyzer'] = $analysis->getSearchAnalyzerName() ?? $analysis->getAnalyzerName();
        }

        return $config;
    }
}
