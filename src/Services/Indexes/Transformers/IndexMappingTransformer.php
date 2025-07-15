<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Transformers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;

class IndexMappingTransformer
{
    public function __construct(
        private readonly IndexSettingsTransformer $indexSettingsTransformer,
        private readonly TextualAnalysisTransformer $textualAnalysisTransformer,
    ) {}

    /**
     * @return mixed[]
     */
    public function getElasticsearchConfiguration(IndexMappingInterface $indexMapping): array
    {
        $settings = $this->indexSettingsTransformer->transform($indexMapping->getIndexSettings());
        $textualAnalysisSettings = $this->textualAnalysisTransformer->transform($indexMapping);

        $mapping = $this->transformMapping($indexMapping);

        return [
            'mappings' => $mapping,
            'settings' => array_merge($settings, $textualAnalysisSettings),
        ];
    }

    /**
     * @return array{'properties': array<string, mixed>, 'dynamic_templates': array<string, mixed>}
     */
    private function transformMapping(IndexMappingInterface $indexMapping): array
    {
        $mappings = [
            'properties' => [],
        ];

        $mappings = array_merge_recursive($indexMapping->getExplicitMapping(), $mappings);
        $dynamicTemplates = $indexMapping->getDynamicTemplates();
        $mappings += [
            'dynamic_templates' => $dynamicTemplates,
        ];

        $mappingProperties = [];

        foreach ($indexMapping->getFields() as $field) {
            $mappingProperties[$field->getPath()] = $field->getElasticsearchConfiguration();
        }
        $mappings['properties'] += $mappingProperties;

        return $mappings;
    }
}
