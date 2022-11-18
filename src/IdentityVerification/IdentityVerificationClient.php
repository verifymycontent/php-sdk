<?php

namespace VerifyMyContent\SDK\IdentityVerification;

use VerifyMyContent\SDK\Core\ExportableClient;
use VerifyMyContent\SDK\IdentityVerification\Entity\Requests\CreateIdentityVerificationRequest;
use VerifyMyContent\SDK\IdentityVerification\Entity\Responses\CreateIdentityVerificationResponse;
use VerifyMyContent\SDK\IdentityVerification\Entity\Responses\GetIdentityVerificationResponse;

interface IdentityVerificationClient extends ExportableClient
{
    const IDENTITY_VERIFICATION_API_VERSION = 'v1';
    const IDENTITY_VERIFICATION_API_VERSIONS = [
        'v1' => IdentityVerificationClientV1::class,
    ];

    const IDENTITY_VERIFICATION_API_PRODUCTION_URL = 'https://oauth.verifymycontent.com';
    const IDENTITY_VERIFICATION_API_SANDBOX_URL = 'https://oauth.sandbox.verifymycontent.com';

    public function createIdentityVerification(CreateIdentityVerificationRequest $request): CreateIdentityVerificationResponse;

    public function getIdentityVerification(string $id): GetIdentityVerificationResponse;
}
