<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Fields;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\SearchOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SearchOptions::class)]
class SearchOptionsTest extends TestCase
{
    public function testSearchOptionsAccessors(): void
    {
        $boost = 100;
        $searchOptions = new SearchOptions($boost);

        $this->assertEquals($boost, $searchOptions->getBoost());
    }
}
