<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Sanitizers;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Text\SearchableKeywordField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\Text\SearchableTextField;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScopeService;
use Adriballa\SymfonySearchBundle\Services\Search\Sanitizers\SearchFieldsSanitizer;
use Adriballa\SymfonySearchBundle\Services\Search\SearchField;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SearchFieldsSanitizer::class)]
class SearchFieldsSanitizerTest extends TestCase
{
    private SearchFieldsSanitizer $sanitizer;

    protected function setUp(): void
    {
        $fieldScopeService = new FieldScopeService();

        $this->sanitizer = new SearchFieldsSanitizer($fieldScopeService);
    }

    /**
     * @dataProvider sanitizeProvider
     *
     * @param string[]                   $inputFields
     * @param FieldDefinitionInterface[] $fieldDefinitions
     * @param SearchField[]              $expectedResults
     */
    public function testSanitize(
        array $inputFields,
        array $fieldDefinitions,
        array $expectedResults,
    ): void {
        $result = $this->sanitizer->sanitize($inputFields, $fieldDefinitions);

        $this->assertEquals($expectedResults, $result);
    }

    /**
     * @return mixed[]
     */
    public static function sanitizeProvider(): array
    {
        return [
            'empty searchFields uses defaults' => [
                'inputFields' => [],
                'fieldDefinitions' => [
                    'title' => new SearchableTextField(path: 'title', boost: 2, scope: FieldScope::Public),
                    'desc' => new SearchableTextField(path: 'desc', boost: 1, scope: FieldScope::Private),
                    'tag' => new SearchableKeywordField(path: 'tag'),
                ],
                'expectedResults' => [new SearchField('title', 2)],
            ],
            'explicit fields with partial accessibility' => [
                'inputFields' => ['title', 'desc'],
                'fieldDefinitions' => [
                    'title' => new SearchableTextField(path: 'title', boost: 2, scope: FieldScope::Public),
                    'desc' => new SearchableTextField(path: 'desc', boost: 1, scope: FieldScope::Private),
                ],
                'expectedResults' => [new SearchField('title', 2)],
            ],
            'unknown or inaccessible fields are skipped' => [
                'inputFields' => ['foo'],
                'fieldDefinitions' => [],
                'expectedResults' => [],
            ],
        ];
    }
}
