<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Aggregations;

interface AggregationInterface extends AggregableInterface
{
    public function getFieldName(): string;

    public function getName(): string;
}
