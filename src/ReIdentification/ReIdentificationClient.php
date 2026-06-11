<?php

namespace VerifyMyContent\SDK\ReIdentification;

use VerifyMyContent\Commons\Security\HMAC;
use VerifyMyContent\SDK\Core\ExportableClient;
use VerifyMyContent\SDK\ReIdentification\Entity\Requests\CreateReIdentificationRequest;
use VerifyMyContent\SDK\ReIdentification\Entity\Responses\CreateReIdentificationResponse;
use VerifyMyContent\SDK\ReIdentification\Entity\Responses\GetReIdentificationResponse;

interface ReIdentificationClient extends ExportableClient
{
    const API_VERSION_V1 = 'v1';
    const API_VERSIONS = [
        self::API_VERSION_V1 => ReIdentificationClientV1::class,
    ];

    const PRODUCTION_URL = 'https://oauth.verifymycontent.com';
    const SANDBOX_URL = 'https://oauth.sandbox.verifymycontent.com';

    public function createReIdentification(CreateReIdentificationRequest $request): CreateReIdentificationResponse;

    public function getReIdentification(string $id): GetReIdentificationResponse;

    public function __construct(HMAC $hmac);
}
