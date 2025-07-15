<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinition;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\SearchOptions;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;

class KeywordField extends FieldDefinition
{
    public function __construct(
        string $path,
        ?SearchOptions $searchOptions = null,
        FieldScope $scope = FieldScope::Public,
        bool $sortable = false,
    ) {
        parent::__construct(
            path: $path,
            type: FieldType::Keyword,
            scope: $scope,
            searchOptions: $searchOptions,
            sortable: $sortable,
        );
    }
}
