<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Sanitizers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScopeServiceInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\Aggregation;
use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\AggregationInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\DateAggregation;
use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\RangeAggregation;

class AggregationsSanitizer
{
    public function __construct(private FieldScopeServiceInterface $fieldScopeService) {}

    /**
     * @param  AggregationInterface[]     $aggregations
     * @param  FieldDefinitionInterface[] $fieldDefinitions
     * @return AggregationInterface[]
     */
    public function sanitize(array $aggregations, array $fieldDefinitions): array
    {
        $sanitizedAggregations = [];

        foreach ($aggregations as $aggregation) {
            if (!$this->isAccessible($aggregation, $fieldDefinitions)) {
                continue;
            }

            if (!$aggregation instanceof Aggregation) {
                $sanitizedAggregations[] = $aggregation;

                continue;
            }

            switch ($fieldDefinitions[$aggregation->getFieldName()]->getType()) {
                case FieldType::SearchableText:
                    $aggregation->addSuffix('keyword');

                    // no break
                case FieldType::Boolean:
                case FieldType::Keyword:
                    $sanitizedAggregations[] = $aggregation;

                    break;

                case FieldType::Float:
                case FieldType::Scaled_float:
                case FieldType::Long:
                    $sanitizedAggregations[] = ($aggregation instanceof RangeAggregation) ? $aggregation : new RangeAggregation($aggregation->getName());

                    break;

                case FieldType::Date:
                    $sanitizedAggregations[] = ($aggregation instanceof DateAggregation) ? $aggregation : new DateAggregation($aggregation->getName());

                    break;

                default:
                    break;
            }
        }

        return $sanitizedAggregations;
    }

    /**
     * @param FieldDefinitionInterface[] $fieldDefinitions
     */
    private function isAccessible(AggregationInterface $aggregation, array $fieldDefinitions): bool
    {
        return isset($fieldDefinitions[$aggregation->getFieldName()]) && $this->fieldScopeService->isAccessible($fieldDefinitions[$aggregation->getFieldName()]->getScope());
    }
}
