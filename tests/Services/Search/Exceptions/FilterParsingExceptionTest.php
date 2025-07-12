<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Exceptions;

use Adriballa\SymfonySearchBundle\Services\Search\Exceptions\FilterParsingException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FilterParsingException::class)]
class FilterParsingExceptionTest extends TestCase
{
    public function testException(): void
    {
        $field = 'test_field';
        $value = 'test_value';
        $exception = new FilterParsingException($field, $value);

        $this->expectException(FilterParsingException::class);
        $this->expectExceptionMessage(sprintf('Filter parsing error for field "%s" and "%s".', $field, $value));

        throw $exception;
    }
}
