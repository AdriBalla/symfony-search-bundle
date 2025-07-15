<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Scopes;

use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\IndexScope;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\IndexScopeService;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\IndexScopeServiceInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexScopeService::class)]
class IndexScopeServiceTest extends TestCase
{
    private IndexScopeServiceInterface $indexScopeService;

    public function setUp(): void
    {
        $this->indexScopeService = new IndexScopeService();
    }

    /**
     * @dataProvider scopeDataProvider
     */
    public function testIsAccessible(IndexScope $scope, bool $expected): void
    {
        $this->assertEquals($expected, $this->indexScopeService->isAccessible($scope));
    }

    /**
     * @return mixed[]
     */
    public static function scopeDataProvider(): array
    {
        return [
            'public scope' => [
                'scope' => IndexScope::Public,
                'expected' => true,
            ],
            'private scope' => [
                'scope' => IndexScope::Private,
                'expected' => false,
            ],
        ];
    }
}
