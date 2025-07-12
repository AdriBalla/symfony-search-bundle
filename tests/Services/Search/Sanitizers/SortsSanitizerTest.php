<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Sanitizers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\KeywordField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Text\SearchableTextField;
use Adriballa\SymfonySearchBundle\Services\Search\Enums\SortDirection;
use Adriballa\SymfonySearchBundle\Services\Search\Sanitizers\SortsSanitizer;
use Adriballa\SymfonySearchBundle\Services\Search\Sort\Sort;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SortsSanitizer::class)]
class SortsSanitizerTest extends TestCase
{
    private SortsSanitizer $sortsSanitizer;

    public function setUp(): void
    {
        parent::setUp();

        $this->sortsSanitizer = new SortsSanitizer();
    }

    public function testSanitize(): void
    {
        $configurationFields = [
            'id' => new KeywordField('id', sortable: true),
            'name' => new SearchableTextField('name'),
            'name_sortable' => new SearchableTextField('name_sortable', sortable: true),
        ];

        $sorts = [
            new Sort('id', SortDirection::ASC),
            new Sort('name', SortDirection::ASC),
            new Sort('name_sortable', SortDirection::ASC),
            new Sort('name_sortable', SortDirection::DESC),
            new Sort('missing-field', SortDirection::ASC),
            new Sort('_score', SortDirection::ASC),
        ];

        $expectedSorts = [
            new Sort('id', SortDirection::ASC),
            new Sort('name_sortable.keyword', SortDirection::ASC),
            new Sort('name_sortable.keyword', SortDirection::DESC),
            new Sort('_score', SortDirection::ASC),
        ];

        $this->assertEquals($expectedSorts, $this->sortsSanitizer->sanitize($sorts, $configurationFields));
    }
}
