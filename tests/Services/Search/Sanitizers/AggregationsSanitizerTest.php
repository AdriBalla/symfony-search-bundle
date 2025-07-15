<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Sanitizers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\BooleanField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\DateField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\FloatField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\KeywordField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\LongField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Basics\ObjectField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Text\SearchableKeywordField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Text\SearchableTextField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScopeService;
use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\Aggregation;
use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\AggregationInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\DateAggregation;
use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\RangeAggregation;
use Adriballa\SymfonySearchBundle\Services\Search\Sanitizers\AggregationsSanitizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AggregationsSanitizer::class)]
class AggregationsSanitizerTest extends TestCase
{
    private AggregationsSanitizer $sanitizer;

    public function setUp(): void
    {
        $fieldScopeService = new FieldScopeService();

        $this->sanitizer = new AggregationsSanitizer($fieldScopeService);
    }

    public function testSanitize(): void
    {
        $configurationFields = [
            'created_at' => new DateField('created_at'),
            'name' => new SearchableTextField('name'),
            'ref' => new KeywordField('ref'),
            'type' => new SearchableKeywordField('type'),
            'score' => new FloatField('score'),
            'quantity' => new LongField('quantity'),
            'active' => new BooleanField('active', searchable: true, sortable: true),
            'private_score' => new FloatField('score', false, false, FieldScope::Private),
            'attributes' => new ObjectField(path: 'attributes', properties: []),
        ];

        $specialAggregation = $this->createMock(AggregationInterface::class);
        $specialAggregation->expects($this->exactly(2))->method('getFieldName')->willReturn('name');

        $aggregations = [
            new Aggregation('created_at'),
            new Aggregation('quantity'),
            new Aggregation('test-aggregation'),
            new Aggregation('name'),
            new Aggregation('score'),
            new Aggregation('type'),
            new Aggregation('undefined'),
            new RangeAggregation('private_score'),
            new Aggregation('ref'),
            new Aggregation('attributes'),
            $specialAggregation,
        ];

        $expectedAggregations = [
            new DateAggregation('created_at'),
            new RangeAggregation('quantity'),
            new Aggregation('name.keyword'),
            new RangeAggregation('score'),
            new Aggregation('type'),
            new Aggregation('ref'),
            $specialAggregation,
        ];

        $this->assertEquals($expectedAggregations, $this->sanitizer->sanitize($aggregations, $configurationFields));
    }
}
