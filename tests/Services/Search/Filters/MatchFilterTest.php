<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Filters;

use Adriballa\SymfonySearchBundle\Services\Search\Filters\MatchFilter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MatchFilter::class)]
class MatchFilterTest extends TestCase
{
    private MatchFilter $matchFilter;

    public function setUp(): void
    {
        $this->matchFilter = new MatchFilter('field', 'value');
    }

    public function testConstructorAndAccessors(): void
    {
        $this->assertEquals('field', $this->matchFilter->getField());
        $this->assertEquals('value', $this->matchFilter->getValue());

        $this->matchFilter->setField('test_setField');
        $this->assertEquals('test_setField', $this->matchFilter->getField());
    }

    public function testToElasticsearchFilter(): void
    {
        $expected = [
            'match' => [
                'field' => [
                    'query' => 'value',
                    'operator' => 'and',
                ],
            ],
        ];

        $this->assertEquals($expected, $this->matchFilter->toElasticsearchFilter());
    }
}
