<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Controller\Search\Request;

use Symfony\Component\Validator\Constraints as Assert;

class SearchIndexRequest
{
    /**
     * @param null|string $query
     * @param int         $start
     * @param int         $size
     * @param string[]    $filtersBy
     * @param string[]    $aggregatesBy
     * @param string[]    $sortsBy
     * @param string[]    $searchFields
     */
    public function __construct(
        #[Assert\Type(type: 'string')]
        public ?string $query = null,
        #[Assert\Type(type: 'array')]
        public array $searchFields = [],
        #[Assert\Type('integer')]
        #[Assert\PositiveOrZero]
        public int $start = 0,
        #[Assert\Type('integer')]
        #[Assert\PositiveOrZero]
        public int $size = 10,
        #[Assert\Type('array')]
        public array $filtersBy = [],
        #[Assert\Type('array')]
        public array $aggregatesBy = [],
        #[Assert\Type('array')]
        public array $sortsBy = [],
    ) {}
}
