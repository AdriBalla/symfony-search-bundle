<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Client;

use Adriballa\SymfonySearchBundle\Services\Indexes\Client\IndexMappingClient;
use Adriballa\SymfonySearchBundle\Services\Indexes\Client\IndexMappingClientInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\FieldInfo;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Managers\IndexMappingManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexMappingClient::class)]
class IndexMappingClientTest extends TestCase
{
    private IndexMappingManagerInterface&MockObject $indexMappingManager;

    private IndexMappingClientInterface $client;

    public function setup(): void
    {
        $this->indexMappingManager = $this->createMock(IndexMappingManagerInterface::class);

        $this->client = new IndexMappingClient($this->indexMappingManager);
    }

    public function testGetFilterableFields(): void
    {
        $index = $this->createMock(Index::class);
        $filters = [$this->createMock(FieldInfo::class)];

        $this->indexMappingManager->expects($this->once())
            ->method('getFilterableFields')
            ->with($index)
            ->willReturn($filters)
        ;

        $result = $this->client->getFilterableFields($index);

        $this->assertEquals($filters, $result);
    }

    public function testGetSortableFields(): void
    {
        $index = $this->createMock(Index::class);
        $sorts = [$this->createMock(FieldInfo::class)];

        $this->indexMappingManager->expects($this->once())
            ->method('getSortableFields')
            ->with($index)
            ->willReturn($sorts)
        ;

        $result = $this->client->getSortableFields($index);

        $this->assertEquals($sorts, $result);
    }
}
