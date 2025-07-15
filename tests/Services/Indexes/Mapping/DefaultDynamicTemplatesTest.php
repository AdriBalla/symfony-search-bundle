<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Mapping;

use Adriballa\SymfonySearchBundle\Tests\Stubs\TestIndexMapping;
use PHPUnit\Framework\TestCase;

class DefaultDynamicTemplatesTest extends TestCase
{
    public function testGetDynamicTemplates(): void
    {
        $indexMapping = new TestIndexMapping();
        $expected = [
            [
                'strings' => [
                    'match' => '*',
                    'match_mapping_type' => 'string',
                    'mapping' => [
                        'type' => 'keyword',
                        'index' => false,
                    ],
                ],
            ],
            [
                'not_indexed' => [
                    'match' => '*',
                    'mapping' => [
                        'index' => false,
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $indexMapping->getDynamicTemplates());
    }
}
