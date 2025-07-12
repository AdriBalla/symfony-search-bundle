<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Services\Indexes\DTO;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\FieldInfo;
use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FieldInfo::class)]
class FieldInfoTest extends TestCase
{
    public function testFieldInfo(): void
    {
        $path = 'name';
        $type = FieldType::Float;

        $fieldInfo = new FieldInfo($path, $type);

        $this->assertEquals($path, $fieldInfo->path);
        $this->assertEquals($type, $fieldInfo->type);
    }
}
