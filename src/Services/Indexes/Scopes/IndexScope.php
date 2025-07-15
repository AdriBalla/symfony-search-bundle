<?php

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Scopes;

enum IndexScope: string
{
    case Public = 'public';
    case Private = 'private';
}
