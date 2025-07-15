<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Stubs;

use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\DefaultDynamicTemplates;
use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Setting\IndexSettings;

class TestIndexMapping implements IndexMappingInterface
{
    use DefaultDynamicTemplates;

    public function getIndexSettings(): IndexSettings
    {
        return new IndexSettings();
    }

    public function getExplicitMapping(): array
    {
        return [];
    }

    public function getFields(): array
    {
        return [];
    }
}
