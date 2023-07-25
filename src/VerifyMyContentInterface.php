<?php

namespace VerifyMyContent\SDK;

use VerifyMyContent\SDK\Complaint\ComplaintClient;
use VerifyMyContent\SDK\ContentModeration\ContentModerationClient;
use VerifyMyContent\SDK\Core\ExportableClient;
use VerifyMyContent\SDK\IdentityVerification\IdentityVerificationClient;

interface VerifyMyContentInterface extends ExportableClient
{
    /**
     * @param array $urls
     * @return void
     */
    public function addRedirectUrls(array $urls):void;

    /**
     * @param array $urls
     * @return void
     */
    public function removeRedirectUrls(array $urls):void;
    /**
     * @return IdentityVerificationClient
     */
    public function identityVerification(): IdentityVerificationClient;

    public function contentModeration(): ContentModerationClient;

    public function complaint(): ComplaintClient;

    /**
     * @param string|IdentityVerificationClient $client
     * @return void
     */
    public function setIdentityVerificationClient($client): void;

    /**
     * @param string|ContentModerationClient $client
     * @return void
     */
    public function setContentModerationClient($client): void;

    /**
     * @param string|ComplaintClient $client
     * @return void
     */
    public function setComplaintClient($client): void;
}
