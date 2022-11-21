<?php

namespace VerifyMyContent\SDK\ContentModeration;

use VerifyMyContent\Commons\Transport\InvalidStatusCodeException;
use VerifyMyContent\SDK\ContentModeration\Entity\Requests\CreateLiveContentModerationRequest;
use VerifyMyContent\SDK\ContentModeration\Entity\Requests\CreateStaticContentModerationRequest;
use VerifyMyContent\SDK\ContentModeration\Entity\Responses\CreateLiveContentModerationResponse;
use VerifyMyContent\SDK\ContentModeration\Entity\Responses\CreateStaticContentModerationResponse;
use VerifyMyContent\SDK\ContentModeration\Entity\Responses\GetStaticContentModerationParticipantsResponse;
use VerifyMyContent\SDK\ContentModeration\Entity\Responses\GetStaticContentModerationResponse;
use VerifyMyContent\SDK\Core\ExportableClient;
use VerifyMyContent\SDK\Core\Validator\ValidationException;

interface ContentModerationClient extends ExportableClient
{
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
     * @return GetStaticContentModerationParticipantsResponse
     * @throws InvalidStatusCodeException
     * @throws ValidationException
     */
    public function getStaticContentModerationParticipants(string $id): GetStaticContentModerationParticipantsResponse;

    /**
     * @param string $id
     * @return void
     * @throws InvalidStatusCodeException
     */
    public function startLiveContentModeration(string $id): void;

    /**
     * @param CreateLiveContentModerationRequest $request
     * @return CreateLiveContentModerationResponse
     * @throws InvalidStatusCodeException
     * @throws ValidationException
     */
    public function createLiveContentModeration(CreateLiveContentModerationRequest $request): CreateLiveContentModerationResponse;
}
