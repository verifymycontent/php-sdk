<?php

namespace VerifyMyContent\SDK\Complaint;

use VerifyMyContent\Commons\Transport\InvalidStatusCodeException;
use VerifyMyContent\SDK\Complaint\Entity\Requests\CreateConsentComplaintRequest;
use VerifyMyContent\SDK\Complaint\Entity\Requests\CreateLiveContentComplaintRequest;
use VerifyMyContent\SDK\Complaint\Entity\Requests\CreateStaticContentComplaintRequest;
use VerifyMyContent\SDK\Complaint\Entity\Responses\CreateConsentComplaintResponse;
use VerifyMyContent\SDK\Complaint\Entity\Responses\CreateLiveContentComplaintResponse;
use VerifyMyContent\SDK\Complaint\Entity\Responses\CreateStaticContentComplaintResponse;
use VerifyMyContent\SDK\Core\ExportableClient;
use VerifyMyContent\SDK\Core\Validator\ValidationException;

interface ComplaintClient extends ExportableClient
{
    const API_VERSION_V1 = 'v1';
    const API_VERSIONS = [
        self::API_VERSION_V1 => ComplaintClientV1::class,
    ];

    const PRODUCTION_URL = 'https://moderation.verifymycontent.com';
    const SANDBOX_URL = 'https://moderation.sandbox.verifymycontent.com';

    /**
     * @param CreateConsentComplaintRequest $request
     * @return CreateConsentComplaintResponse
     * @throws InvalidStatusCodeException
     * @throws ValidationException
     */
    public function createConsentComplaint(CreateConsentComplaintRequest $request): CreateConsentComplaintResponse;

    /**
     * @param CreateStaticContentComplaintRequest $request
     * @return CreateStaticContentComplaintResponse
     * @throws InvalidStatusCodeException
     * @throws ValidationException
     */
    public function createStaticContentComplaint(CreateStaticContentComplaintRequest $request): CreateStaticContentComplaintResponse;

    /**
     * @param CreateLiveContentComplaintRequest $request
     * @return CreateLiveContentComplaintResponse
     * @throws InvalidStatusCodeException
     * @throws ValidationException
     */
    public function createLiveContentComplaint(CreateLiveContentComplaintRequest $request): CreateLiveContentComplaintResponse;
}
