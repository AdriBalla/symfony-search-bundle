<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Documents\DTO;

use Adriballa\SymfonySearchBundle\Services\Documents\DTO\MultipleIndexationResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MultipleIndexationResponse::class)]
class MultipleIndexationResponseTest extends TestCase
{
    /**
     * @dataProvider  dataProvider
     * @param mixed[] $errors
     */
    public function testConstructor(int $total, int $failure, ?array $errors): void
    {
        $multipleIndexationResponse = new MultipleIndexationResponse($total, $failure, $errors);

        $this->assertEquals($total, $multipleIndexationResponse->total);
        $this->assertEquals($failure, $multipleIndexationResponse->failure);
        $this->assertEquals($errors, $multipleIndexationResponse->errors);
    }

    /**
     * @return mixed[]
     */
    public static function dataProvider(): array
    {
        return [
            'success response' => [
                'total' => 100,
                'failure' => 0,
                'errors' => null,
            ],
            'error response' => [
                'total' => 200,
                'failure' => 10,
                'errors' => [
                    456 => 'an error occurred',
                    775 => 'something wrong happened',
                ],
            ],
        ];
    }
}
