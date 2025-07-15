<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Exceptions;

use Adriballa\SymfonySearchBundle\Services\Search\Exceptions\FilterException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FilterException::class)]
class FilterExceptionTest extends TestCase
{
    public function testException(): void
    {
        $message = 'this is an error';
        $exception = new FilterException($message);

        $this->expectException(FilterException::class);
        $this->expectExceptionMessage($message);

        throw $exception;
    }
}
