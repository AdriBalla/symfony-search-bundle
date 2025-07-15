<?php

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Fields;

enum FieldType: string
{
    case SearchableText = 'text';
    case Keyword = 'keyword';
    case Float = 'float';
    case Long = 'long';
    case Boolean = 'boolean';
    case Date = 'date';
    case Object = 'object';
    case Nested = 'nested';
    case GeoPoint = 'geo_point';
    case Scaled_float = 'scaled_float';
}
