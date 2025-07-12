<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\DTO;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\IndexMapping;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexMapping::class)]
class IndexMappingTest extends TestCase
{
    public function testIndexMappingAccessors(): void
    {
        $name = 'index_type';
        $configuration = [
            'fields' => [
                'doc' => true,
            ],
        ];

        $mapping = new IndexMapping($name, $configuration);

        $this->assertEquals($name, $mapping->name);
        $this->assertEquals($configuration, $mapping->configuration);
    }
}
