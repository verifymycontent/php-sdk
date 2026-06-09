<?php

namespace VerifyMyContent\SDK\ReIdentification\Exception;

use RuntimeException;

class FeatureNotEnabledException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('re-identification feature not enabled', 403);
    }
}
