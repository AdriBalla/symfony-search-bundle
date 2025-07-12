<?php

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Text;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\KeywordField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\SearchOptions;

class SearchableKeywordField extends KeywordField
{
    public function __construct(
        string $path,
        bool $sortable = false,
    ) {
        parent::__construct(
            path: $path,
            searchOptions: new SearchOptions(),
            sortable: $sortable,
        );
    }
}
