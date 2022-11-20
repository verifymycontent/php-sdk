<?php

namespace VerifyMyContent\SDK\ContentModeration;

interface ContentModerationStatus
{
    const APPROVED = 'Approved';
    const REJECTED = 'Rejected';
    const FAILED = 'Failed';
    const AWAITING_PEOPLE = 'Awaiting People';
    const AWAITING_AUTOMATION = 'Awaiting Automation';
    const AWAITING_MODERATION = 'Awaiting Moderation';
}
