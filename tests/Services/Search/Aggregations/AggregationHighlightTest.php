<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Aggregations;

use Adriballa\SymfonySearchBundle\Services\Search\Aggregations\AggregationHighlight;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AggregationHighlight::class)]
class AggregationHighlightTest extends TestCase
{
    /**
     * @dataProvider aggregationHighlightDataProvider
     *
     * @param string[] $tags
     * @param mixed[]  $expected
     */
    public function testToElasticSearchHighlight(?array $tags, array $expected): void
    {
        if (!empty($tags)) {
            $preTag = $tags['preTag'];
            $postTag = $tags['postTag'];
            $highlight = new AggregationHighlight($preTag, $postTag);
        } else {
            $highlight = new AggregationHighlight();
            $preTag = '<em>';
            $postTag = '</em>';
        }

        $this->assertEquals($preTag, $highlight->getHighlightPreTag());
        $this->assertEquals($postTag, $highlight->getHighlightPostTag());
        $this->assertEquals($expected, $highlight->toElasticsearchHighlight());
    }

    /**
     * @return mixed[]
     */
    public static function aggregationHighlightDataProvider(): array
    {
        return [
            'default' => [
                'tags' => [
                ],
                'expected' => [
                    'pre_tags' => ['<em>'],
                    'post_tags' => ['</em>'],
                ],
            ],
            'custom' => [
                'tags' => [
                    'preTag' => '<b>',
                    'postTag' => '</b>',
                ],
                'expected' => [
                    'pre_tags' => ['<b>'],
                    'post_tags' => ['</b>'],
                ],
            ],
        ];
    }
}
