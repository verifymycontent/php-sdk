<?php

namespace VerifyMyContent\SDK\IdentityVerification;

use VerifyMyContent\Commons\Security\HMAC;
use VerifyMyContent\SDK\Core\ExportableClient;
use VerifyMyContent\SDK\IdentityVerification\Entity\Requests\CreateIdentityVerificationRequest;
use VerifyMyContent\SDK\IdentityVerification\Entity\Responses\CreateIdentityVerificationResponse;
use VerifyMyContent\SDK\IdentityVerification\Entity\Responses\GetIdentityVerificationResponse;

interface IdentityVerificationClient extends ExportableClient
{
    const API_VERSION_V1 = 'v1';
    const API_VERSIONS = [
        self::API_VERSION_V1 => IdentityVerificationClientV1::class,
    ];

    const PRODUCTION_URL = 'https://oauth.verifymycontent.com';
    const SANDBOX_URL = 'https://oauth.sandbox.verifymycontent.com';

    public function createIdentityVerification(CreateIdentityVerificationRequest $request): CreateIdentityVerificationResponse;

    public function getIdentityVerification(string $id): GetIdentityVerificationResponse;

    public function __construct(HMAC $hmac);
}
