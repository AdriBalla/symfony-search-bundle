<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinition;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\SearchOptions;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;

class ScaledFloatField extends FieldDefinition
{
    public function __construct(
        string $path,
        bool $sortable = false,
        bool $searchable = true,
        FieldScope $scope = FieldScope::Public,
    ) {
        parent::__construct(
            path: $path,
            type: FieldType::Scaled_float,
            scope: $scope,
            searchOptions: $searchable ? new SearchOptions() : null,
            sortable: $sortable,
        );
    }
}
