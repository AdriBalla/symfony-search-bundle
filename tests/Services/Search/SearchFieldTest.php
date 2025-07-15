<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search;

use Adriballa\SymfonySearchBundle\Services\Search\SearchField;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SearchField::class)]
class SearchFieldTest extends TestCase
{
    public function testSearchField(): void
    {
        $field = 'test';
        $boost = 2;

        $searchField = new SearchField($field, $boost);

        $this->assertEquals($field, $searchField->getField());
        $this->assertEquals($boost, $searchField->getBoost());
        $this->assertEquals(sprintf('%s^%s', $field, $boost), $searchField->getElasticsearchFieldString());
    }
}
