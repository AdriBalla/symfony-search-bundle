<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\DTO;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Index::class)]
class IndexTest extends TestCase
{
    public function testIndexAccessors(): void
    {
        $indexType = 'index_type';
        $indexName = 'index_name';

        $index = new Index($indexType, $indexName);

        $this->assertEquals($indexType, $index->getType());
        $this->assertEquals($indexName, $index->getName());
    }
}
