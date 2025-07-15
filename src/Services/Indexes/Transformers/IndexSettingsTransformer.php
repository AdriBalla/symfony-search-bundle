<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Transformers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Setting\IndexSettings;

class IndexSettingsTransformer
{
    /**
     * @return array{'number_of_shards': int, 'number_of_replicas': int, 'refresh_interval': string}
     */
    public function transform(IndexSettings $indexSettings): array
    {
        return [
            'number_of_shards' => $indexSettings->getNbShards(),
            'number_of_replicas' => $indexSettings->getNbReplicas(),
            'refresh_interval' => sprintf('%ss', $indexSettings->getRefreshInterval()),
        ];
    }
}
