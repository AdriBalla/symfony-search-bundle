<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Transformers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Setting\IndexSettings;
use Adriballa\SymfonySearchBundle\Services\Indexes\Transformers\IndexSettingsTransformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexSettingsTransformer::class)]
class IndexSettingsTransformerTest extends TestCase
{
    private IndexSettingsTransformer $transformer;

    public function setUp(): void
    {
        $this->transformer = new IndexSettingsTransformer();
    }

    public function testTransform(): void
    {
        $nbReplicas = 100;
        $nbShards = 200;
        $refreshInterval = 15;

        $settings = $this->createMock(IndexSettings::class);

        $settings->expects($this->once())->method('getNbShards')->willReturn($nbShards);
        $settings->expects($this->once())->method('getNbReplicas')->willReturn($nbReplicas);
        $settings->expects($this->once())->method('getRefreshInterval')->willReturn($refreshInterval);

        $expected = [
            'number_of_shards' => 200,
            'number_of_replicas' => 100,
            'refresh_interval' => '15s',
        ];

        $this->assertEquals($expected, $this->transformer->transform($settings));
    }
}
