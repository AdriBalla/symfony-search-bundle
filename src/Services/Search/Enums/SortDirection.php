<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Enums;

enum SortDirection: string
{
    case ASC = 'asc';

    case DESC = 'desc';
}
