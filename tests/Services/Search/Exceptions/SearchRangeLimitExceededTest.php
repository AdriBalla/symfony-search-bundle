<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Search\Exceptions;

use Adriballa\SymfonySearchBundle\Services\Search\Exceptions\SearchRangeLimitExceeded;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SearchRangeLimitExceeded::class)]
class SearchRangeLimitExceededTest extends TestCase
{
    public function testException(): void
    {
        $position = 1000;
        $pageLimit = 200;

        $exception = new SearchRangeLimitExceeded($position, $pageLimit);

        $this->expectException(SearchRangeLimitExceeded::class);
        $this->expectExceptionMessage(sprintf('The position %s is above the page limit %s', $position, $pageLimit));

        throw $exception;
    }
}
