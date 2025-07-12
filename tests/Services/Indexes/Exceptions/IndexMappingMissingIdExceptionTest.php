<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Exceptions;

use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexMappingMissingIdException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexMappingMissingIdException::class)]
class IndexMappingMissingIdExceptionTest extends TestCase
{
    public function testException(): void
    {
        $exception = new IndexMappingMissingIdException();
        $message = 'Missing keyword field id in index mapping';

        $this->expectException(IndexMappingMissingIdException::class);
        $this->expectExceptionMessage($message);

        throw $exception;
    }
}
