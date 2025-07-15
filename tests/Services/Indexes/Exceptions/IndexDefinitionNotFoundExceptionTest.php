<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Exceptions;

use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexDefinitionNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexDefinitionNotFoundException::class)]
class IndexDefinitionNotFoundExceptionTest extends TestCase
{
    public function testException(): void
    {
        $indexType = 'test_mocks';
        $exception = new IndexDefinitionNotFoundException($indexType);

        $this->expectException(IndexDefinitionNotFoundException::class);
        $this->expectExceptionMessage('Index definition for type test_mocks not found');

        throw $exception;
    }
}
