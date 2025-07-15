<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Mapping;

use Adriballa\SymfonySearchBundle\Services\Indexes\Fields\FieldDefinitionInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Setting\IndexSettings;

interface IndexMappingInterface
{
    public function getIndexSettings(): IndexSettings;

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/dynamic-templates.html
     *
     * @return mixed[]
     */
    public function getDynamicTemplates(): array;

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/explicit-mapping.html
     *
     * @return mixed[]
     */
    public function getExplicitMapping(): array;

    /**
     * Get configured fields.
     *
     * @return FieldDefinitionInterface[]
     */
    public function getFields(): array;
}
