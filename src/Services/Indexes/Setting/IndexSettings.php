<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Setting;

class IndexSettings
{
    public const int  DEFAULT_REFRESH_INTERVAL = 15;

    public function __construct(
        private readonly int $nbReplicas = 1,
        private readonly int $nbShards = 1,
        private readonly int $refreshInterval = self::DEFAULT_REFRESH_INTERVAL,
    ) {}

    public function getNbReplicas(): int
    {
        return $this->nbReplicas;
    }

    public function getNbShards(): int
    {
        return $this->nbShards;
    }

    public function getRefreshInterval(): int
    {
        return $this->refreshInterval;
    }
}
