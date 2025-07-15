<?php

declare(strict_types=1);

namespace Services\Indexes\Exceptions;

use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexMappingDuplicatesPathsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexMappingDuplicatesPathsException::class)]
class IndexMappingDuplicatesPathsExceptionTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     * @param string[] $duplicates
     */
    public function testException(array $duplicates, string $message): void
    {
        $exception = new IndexMappingDuplicatesPathsException($duplicates);

        $this->expectException(IndexMappingDuplicatesPathsException::class);
        $this->expectExceptionMessage($message);

        throw $exception;
    }

    /**
     * @return mixed[]
     */
    public static function dataProvider(): array
    {
        return [
            'single duplicates' => [
                'duplicates' => ['username'],
                'message' => 'Duplicates paths username in index mapping',
            ],
            'index with name' => [
                'duplicates' => ['username', 'mail', 'login'],
                'message' => 'Duplicates paths username,mail,login in index mapping',
            ],
        ];
    }
}
