<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Exceptions;

use Adriballa\SymfonySearchBundle\Services\Search\Exceptions\SortParsingException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SortParsingException::class)]
class SortParsingExceptionTest extends TestCase
{
    public function testException(): void
    {
        $input = 'this is the input';
        $exception = new SortParsingException($input);

        $this->expectException(SortParsingException::class);
        $this->expectExceptionMessage(sprintf('Could not parse sort %s".', $input));

        throw $exception;
    }
}
