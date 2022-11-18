<?php

namespace VerifyMyContent\SDK\IdentityVerification;

interface IdentityVerificationStatus
{
    const PENDING = 'pending';
    const STARTED = 'started';
    const EXPIRED = 'expired';
    const FAILED = 'failed';
    const APPROVED = 'approved';
}
