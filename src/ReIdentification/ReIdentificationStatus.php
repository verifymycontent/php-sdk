<?php

namespace VerifyMyContent\SDK\ReIdentification;

interface ReIdentificationStatus
{
    const PENDING = 'pending';
    const STARTED = 'started';
    const EXPIRED = 'expired';
    const FAILED = 'failed';
    const APPROVED = 'approved';
}
