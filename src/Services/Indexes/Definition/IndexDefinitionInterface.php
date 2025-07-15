<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Definition;

use Adriballa\SymfonySearchBundle\Services\Indexes\Mapping\IndexMappingInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Scopes\IndexScope;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('search.index.definition')]
interface IndexDefinitionInterface
{
    public static function getIndexType(): string;

    public function getIndexMapping(): IndexMappingInterface;

    public function getScope(): ?IndexScope;

    public function getPaginationLimit(): int;
}
