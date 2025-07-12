<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Search\Exceptions;

class SearchRangeLimitExceeded extends \Exception
{
    public function __construct(int $position, int $pageLimit)
    {
        parent::__construct(sprintf('The position %s is above the page limit %s', $position, $pageLimit));
    }
}
