<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Scopes;

use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScopeService;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScopeServiceInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FieldScopeService::class)]
class FieldScopeServiceTest extends TestCase
{
    private FieldScopeServiceInterface $fieldScopeService;

    public function setUp(): void
    {
        $this->fieldScopeService = new FieldScopeService();
    }

    /**
     * @dataProvider scopeDataProvider
     */
    public function testIsAccessible(FieldScope $scope, bool $expected): void
    {
        $this->assertEquals($expected, $this->fieldScopeService->isAccessible($scope));
    }

    /**
     * @return mixed[]
     */
    public static function scopeDataProvider(): array
    {
        return [
            'public scope' => [
                'scope' => FieldScope::Public,
                'expected' => true,
            ],
            'private scope' => [
                'scope' => FieldScope::Private,
                'expected' => false,
            ],
        ];
    }
}
