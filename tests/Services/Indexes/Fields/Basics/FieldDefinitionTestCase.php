<?php

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\Fields\Basics;

use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\FieldScope;
use PHPUnit\Framework\TestCase;

class FieldDefinitionTestCase extends TestCase
{
    /**
     * @return mixed[]
     */
    public static function scopeDataProvider(): array
    {
        return [
            'public' => [
                'scope' => FieldScope::Public,
            ],
            'private' => [
                'scope' => FieldScope::Private,
            ],
        ];
    }
}
