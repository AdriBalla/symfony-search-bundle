<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Sanitizers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\FloatField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Text\SearchableKeywordField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Text\SearchableTextField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScopeService;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\AndFilters;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\ExactMatchFilter;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\OrFilters;
use Adriballa\SymfonySearchBundle\Services\Search\Filters\RangeFilter;
use Adriballa\SymfonySearchBundle\Services\Search\Sanitizers\FiltersSanitizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FiltersSanitizer::class)]
class FiltersSanitizerTest extends TestCase
{
    private FiltersSanitizer $filtersSanitizer;

    public function setUp(): void
    {
        parent::setUp();

        $fieldScopeService = new FieldScopeService();

        $this->filtersSanitizer = new FiltersSanitizer($fieldScopeService);
    }

    public function testSanitize(): void
    {
        $configurationFields = [
            'name' => new SearchableTextField('name'),
            'type' => new SearchableKeywordField('type'),
            'score' => new FloatField('score', false, true, FieldScope::Private),
        ];

        $filters = [
            new ExactMatchFilter('test_field', ['test_value']),
            new ExactMatchFilter('name', ['index name']),
            new AndFilters([
                new ExactMatchFilter('name', ['index name']),
                new ExactMatchFilter('definition', ['index definition']),
                new RangeFilter('score', '10', '20'),
            ]),
            new OrFilters([
                new ExactMatchFilter('undefined', ['test']),
                new ExactMatchFilter('type', ['mock']),
            ]),
        ];

        $expectedFilters = [
            new ExactMatchFilter('name.keyword', ['index name']),
            new AndFilters([
                new ExactMatchFilter('name.keyword', ['index name']),
            ]),
            new OrFilters([
                new ExactMatchFilter('type', ['mock']),
            ]),
        ];

        $this->assertEquals($expectedFilters, $this->filtersSanitizer->sanitize($filters, $configurationFields));
    }
}
