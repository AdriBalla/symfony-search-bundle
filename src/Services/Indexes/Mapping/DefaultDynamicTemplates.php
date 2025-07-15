<?php

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Mapping;

trait DefaultDynamicTemplates
{
    /**
     * Gets the default configuration for an Elasticsearch index.
     *
     * @return mixed[]
     */
    public function getDynamicTemplates(): array
    {
        return [
            // Disable auto parsing of strings
            [
                'strings' => [
                    'match' => '*',
                    'match_mapping_type' => 'string',
                    'mapping' => [
                        'type' => 'keyword',
                        'index' => false,
                        // Enabled seems buggy : https://github.com/elastic/elasticsearch/issues/68576
                        // See if this issue is solved in another Elasticsearch version
                        // 'enabled' => false,
                    ],
                ],
            ],
            [
                'not_indexed' => [
                    'match' => '*',
                    'mapping' => [
                        'index' => false,
                    ],
                ],
            ],
        ];
    }
}
