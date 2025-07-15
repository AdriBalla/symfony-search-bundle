<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Documents\DTO;

use Adriballa\SymfonySearchBundle\Services\Documents\DTO\Document;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Document::class)]
class DocumentTest extends TestCase
{
    public function testDocumentAccessors(): void
    {
        $id = '5678';
        $body = ['this is the body'];
        $document = new Document($id, $body);

        $this->assertEquals($id, $document->getId());
        $this->assertEquals($body, $document->getBody());
    }
}
