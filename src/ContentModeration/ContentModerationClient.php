<?php

namespace VerifyMyContent\SDK\ContentModeration;

use VerifyMyContent\Commons\Transport\InvalidStatusCodeException;
use VerifyMyContent\SDK\ContentModeration\Entity\Requests\CreateAnonymousLiveContentModerationRequest;
use VerifyMyContent\SDK\ContentModeration\Entity\Requests\CreateLiveContentModerationRequest;
use VerifyMyContent\SDK\ContentModeration\Entity\Requests\StartLiveContentModerationRequest;
use VerifyMyContent\SDK\ContentModeration\Entity\Requests\CreateStaticContentModerationRequest;
use VerifyMyContent\SDK\ContentModeration\Entity\Requests\ChangeLiveContentRuleRequest;
use VerifyMyContent\SDK\ContentModeration\Entity\Responses\CreateLiveContentModerationResponse;
use VerifyMyContent\SDK\ContentModeration\Entity\Responses\CreateStaticContentModerationResponse;
use VerifyMyContent\SDK\ContentModeration\Entity\Responses\GetLiveContentModerationResponse;
use VerifyMyContent\SDK\ContentModeration\Entity\Responses\GetStaticContentModerationResponse;
use VerifyMyContent\SDK\Core\ExportableClient;
use VerifyMyContent\SDK\Core\Validator\ValidationException;

interface ContentModerationClient extends ExportableClient
{
    const API_VERSION_V1 = 'v1';
    const API_VERSIONS = [
        self::API_VERSION_V1 => ContentModerationClientV1::class,
    ];

    const PRODUCTION_URL = 'https://moderation.verifymycontent.com';
    const SANDBOX_URL = 'https://moderation.sandbox.verifymycontent.com';

    /**
     * @param CreateStaticContentModerationRequest $request
     * @return CreateStaticContentModerationResponse
     * @throws InvalidStatusCodeException
     * @throws ValidationException
     */
    public function createStaticContentModeration(CreateStaticContentModerationRequest $request): CreateStaticContentModerationResponse;

    /**
     * @param string $id
     * @return GetStaticContentModerationResponse
     * @throws InvalidStatusCodeException
     * @throws ValidationException
     */
    public function getStaticContentModeration(string $id): GetStaticContentModerationResponse;

    /**
     * @param string $id
     * @return void
     * @throws InvalidStatusCodeException
     */
    public function startLiveContentModeration(string $id, StartLiveContentModerationRequest $request = null): void;

    /**
     * @param CreateLiveContentModerationRequest $request
     * @return CreateLiveContentModerationResponse
     * @throws InvalidStatusCodeException
     * @throws ValidationException
     */
    public function createLiveContentModeration(CreateLiveContentModerationRequest $request): CreateLiveContentModerationResponse;

    /**
     * @param string $id
     * @return GetLiveContentModerationResponse
     * @throws InvalidStatusCodeException
     * @throws ValidationException
     */
    public function getLiveContentModeration(string $id): GetLiveContentModerationResponse;

    /**
     * @param CreateAnonymousLiveContentModerationRequest $request
     * @return CreateLiveContentModerationResponse
     * @throws InvalidStatusCodeException
     * @throws ValidationException
     */
    public function createAnonymousLiveContentModeration(CreateAnonymousLiveContentModerationRequest $request): CreateLiveContentModerationResponse;

    /**
    * @param string $id
    * @param ChangeLiveContentRuleRequest $request
    * @return void
    * @throws InvalidStatusCodeException
    */
    public function changeLiveContentRule(string $id, ChangeLiveContentRuleRequest $request): void;

    /**
    * @param string $id
    * @return void
    * @throws InvalidStatusCodeException
    */
    public function pauseLivestream(string $id): void;

    /**
    * @param string $id
    * @return void
    * @throws InvalidStatusCodeException
    */
    public function pauseLivestream(string $id): void;
}
