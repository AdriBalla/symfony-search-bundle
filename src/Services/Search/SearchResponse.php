<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search;

class SearchResponse
{
    /**
     * @param string   $indexType
     * @param bool     $success
     * @param null|int $start
     * @param null|int $size
     * @param int      $duration
     * @param int      $totalHits
     * @param mixed[]  $hits
     * @param mixed[]  $aggregations
     */
    public function __construct(
        public string $indexType,
        public bool $success,
        public ?int $start = null,
        public ?int $size = null,
        public int $duration = 0,
        public int $totalHits = 0,
        public array $hits = [],
        public array $aggregations = [],
    ) {}
}
