<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Documents\Transformer;

use Adriballa\SymfonySearchBundle\Services\Documents\DTO\Document;
use Adriballa\SymfonySearchBundle\Services\Documents\Transformer\DocumentTransformer;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Managers\IndexNameManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(DocumentTransformer::class)]
class DocumentTransformerTest extends TestCase
{
    private IndexNameManagerInterface&MockObject $indexNameManager;
    private DocumentTransformer $transformer;

    protected function setUp(): void
    {
        $this->indexNameManager = $this->createMock(IndexNameManagerInterface::class);

        $this->transformer = new DocumentTransformer($this->indexNameManager);
    }

    /**
     * @dataProvider indexDataProvider
     */
    public function testGenerateIndexInstructions(Index $index, string $indexName): void
    {
        $document = $this->createMock(Document::class);

        $this->indexNameManager->expects($this->once())
            ->method('getIndexName')
            ->with($index)
            ->willReturn($indexName)
        ;

        $document->expects($this->exactly(2))
            ->method('getId')
            ->willReturn('doc_1')
        ;

        $document->expects($this->once())
            ->method('getBody')
            ->willReturn(['title' => 'Test Document'])
        ;

        $documents = [$document];

        $generator = $this->transformer->generateIndexInstructions($index, $documents);
        $result = iterator_to_array($generator);

        $this->assertSame([
            [
                'index' => [
                    '_index' => $indexName,
                    '_id' => 'doc_1',
                ],
            ],
            [
                'title' => 'Test Document',
                'id' => 'doc_1',
            ],
        ], $result);
    }

    /**
     * @dataProvider indexDataProvider
     */
    public function testGeneratePartialUpdateInstructions(Index $index, string $indexName): void
    {
        $this->indexNameManager->expects($this->once())
            ->method('getIndexName')
            ->with($index)
            ->willReturn($indexName)
        ;

        $document = $this->createMock(Document::class);
        $document->expects($this->once())
            ->method('getId')
            ->willReturn('doc_3')
        ;

        $document->expects($this->once())
            ->method('getBody')
            ->willReturn(['field' => 'value'])
        ;

        $generator = $this->transformer->generatePartialUpdateInstructions($index, [$document], true);
        $result = iterator_to_array($generator);

        $this->assertSame([
            [
                'update' => [
                    '_index' => $indexName,
                    '_id' => 'doc_3',
                ],
            ],
            [
                'doc' => ['field' => 'value'],
                'doc_as_upsert' => true,
            ],
        ], $result);
    }

    /**
     * @return mixed[]
     */
    public static function indexDataProvider(): array
    {
        return [
            'index with name' => [
                'index' => new Index('mocks', 'mocks-custom-index'),
                'indexName' => 'mocks-custom-index',
            ],
            'index with type only' => [
                'index' => new Index('mocks'),
                'indexName' => 'prefix_mocks',
            ],
        ];
    }
}
