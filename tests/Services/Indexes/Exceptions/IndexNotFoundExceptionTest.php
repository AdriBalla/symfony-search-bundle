<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Exceptions;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexNotFoundException::class)]
class IndexNotFoundExceptionTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testException(Index $index, string $message): void
    {
        $exception = new IndexNotFoundException($index);

        $this->expectException(IndexNotFoundException::class);
        $this->expectExceptionMessage($message);

        throw $exception;
    }

    /**
     * @return mixed[]
     */
    public static function dataProvider(): array
    {
        return [
            'index without name' => [
                'index' => new Index('test_mocks'),
                'message' => 'Index for type test_mocks not found',
            ],
            'index with name' => [
                'index' => new Index('test_mocks', 'test_mocks_name'),
                'message' => 'Index for type test_mocks and name test_mocks_name not found',
            ],
        ];
    }
}
