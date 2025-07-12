<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Aggregations;

class AggregationHighlight
{
    public function __construct(
        private readonly string $highlightPreTag = '<em>',
        private readonly string $highlightPostTag = '</em>',
    ) {}

    public function getHighlightPreTag(): string
    {
        return $this->highlightPreTag;
    }

    public function getHighlightPostTag(): string
    {
        return $this->highlightPostTag;
    }

    /**
     * @return array<string, string[]>
     */
    public function toElasticsearchHighlight(): array
    {
        return [
            'pre_tags' => [$this->highlightPreTag],
            'post_tags' => [$this->highlightPostTag],
        ];
    }
}
