<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Definition;

use Adriballa\SymfonySearchBundle\Services\Indexes\Definition\IndexDefinition;

class TestIndexDefinition extends IndexDefinition
{
    public static function getIndexType(): string
    {
        return 'test_mocks';
    }
}
