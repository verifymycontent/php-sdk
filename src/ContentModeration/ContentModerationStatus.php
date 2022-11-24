<?php

namespace VerifyMyContent\SDK\ContentModeration;

interface ContentModerationStatus
{
    const STATIC_APPROVED = 'Approved';
    const STATIC_REJECTED = 'Rejected';
    const STATIC_FAILED = 'Failed';
    const STATIC_AWAITING_PEOPLE = 'Awaiting People';
    const STATIC_AWAITING_AUTOMATION = 'Awaiting Automation';
    const STATIC_AWAITING_MODERATION = 'Awaiting Moderation';

    const LIVE_WAITING = 'Waiting';
    const LIVE_AUTHORIZED = 'Authorized';
    const LIVE_DENIED = 'Denied';
    const LIVE_STARTED = 'Started';
    const LIVE_FINISHED = 'Finished';
    const LIVE_STOP_REQUESTED = 'Stop Requested';
    const LIVE_HALTED = 'Halted';
    const LIVE_FAILED = 'Failed';
}
