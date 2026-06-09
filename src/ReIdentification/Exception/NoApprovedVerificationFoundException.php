<?php

namespace VerifyMyContent\SDK\ReIdentification\Exception;

use RuntimeException;

class NoApprovedVerificationFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('no approved verification found for customer', 404);
    }
}
