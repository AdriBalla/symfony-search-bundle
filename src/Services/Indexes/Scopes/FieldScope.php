<?php

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Scopes;

enum FieldScope: string
{
    case Public = 'public';
    case Private = 'private';
}
