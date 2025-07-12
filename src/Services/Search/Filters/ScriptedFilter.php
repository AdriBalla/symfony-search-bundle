<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Filters;

class ScriptedFilter implements FilterableInterface
{
    /**
     * @param array<string, mixed> $params
     */
    public function __construct(private readonly string $script, private readonly array $params = []) {}

    public function getScript(): string
    {
        return $this->script;
    }

    /**
     * @return mixed[]
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function toElasticsearchFilter(): array
    {
        return [
            'script' => [
                'script' => [
                    'source' => $this->script,
                    'lang' => 'painless',
                    'params' => $this->params,
                ],
            ],
        ];
    }
}
