<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Documents\DTO;

use Adriballa\SymfonySearchBundle\Services\Documents\DTO\IndexationResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexationResponse::class)]
class IndexationResponseTest extends TestCase
{
    /**
     * @dataProvider  dataProvider
     * @param string[] $errors
     */
    public function testConstructor(bool $success, ?array $errors): void
    {
        $indexationResponse = new IndexationResponse($success, $errors);

        $this->assertEquals($success, $indexationResponse->success);
        $this->assertEquals($errors, $indexationResponse->errors);
    }

    /**
     * @return mixed[]
     */
    public static function dataProvider(): array
    {
        return [
            'success response' => [
                'success' => true,
                'errors' => null,
            ],
            'error response' => [
                'success' => false,
                'errors' => ['an error occurred'],
            ],
        ];
    }
}
