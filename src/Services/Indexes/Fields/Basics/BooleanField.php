<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinition;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\SearchOptions;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;

class BooleanField extends FieldDefinition
{
    public function __construct(
        string $path,
        FieldScope $scope = FieldScope::Public,
        bool $searchable = false,
        bool $sortable = false,
    ) {
        parent::__construct(
            path: $path,
            type: FieldType::Boolean,
            scope: $scope,
            searchOptions: $searchable ? new SearchOptions() : null,
            sortable: $sortable,
        );
    }
}
