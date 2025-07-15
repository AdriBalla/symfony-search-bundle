<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Settings;

use Adriballa\SymfonySearchBundle\Services\Indexes\Setting\IndexSettings;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexSettings::class)]
class IndexSettingsTest extends TestCase
{
    public function testConstructAndAccessors(): void
    {
        $settings = new IndexSettings(
            100,
            200,
            60,
        );

        $this->assertEquals(100, $settings->getNbReplicas());
        $this->assertEquals(200, $settings->getNbShards());
        $this->assertEquals(60, $settings->getRefreshInterval());
    }
}
