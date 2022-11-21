<?php

namespace VerifyMyContent\SDK\ContentModeration;

use VerifyMyContent\Commons\Security\HMAC;
use VerifyMyContent\Commons\Transport\HTTP;
use VerifyMyContent\Commons\Transport\InvalidStatusCodeException;
use VerifyMyContent\SDK\ContentModeration\Entity\Requests\CreateStaticContentModerationRequest;
use VerifyMyContent\SDK\ContentModeration\Entity\Responses\CreateStaticContentModerationResponse;
use VerifyMyContent\SDK\ContentModeration\Entity\Responses\GetStaticContentModerationParticipantsResponse;
use VerifyMyContent\SDK\ContentModeration\Entity\Responses\GetStaticContentModerationResponse;
use VerifyMyContent\SDK\Core\Validator\ValidationException;

final class ContentModerationClientV1 implements ContentModerationClient
{
    const ENDPOINT_CREATE_STATIC_CONTENT_MODERATION = '/api/v1/moderation';
    const ENDPOINT_GET_STATIC_CONTENT_MODERATION = '/api/v1/moderation/%s';
    const ENDPOINT_GET_STATIC_CONTENT_MODERATION_PARTICIPANTS = '/api/v1/moderation/%s/participants';

    /**
     * @var HTTP $transport
     */
    private $transport;

    /**
     * @var HMAC
     */
    private $hmac;

    public function __construct(HMAC $hmac)
    {
        $this->hmac = $hmac;
        $this->transport = new HTTP(ContentModerationClient::PRODUCTION_URL);
    }

    public function useSandbox(): void
    {
        $this->setBaseURL(ContentModerationClient::SANDBOX_URL);
    }

    public function setBaseURL(string $baseURL): void
    {
        $this->transport->setBaseUrl($baseURL);
    }

    /**
     * @param HTTP $transport
     */
    public function setTransport(HTTP $transport): void
    {
        $this->transport = $transport;
    }

    /**
     * @param CreateStaticContentModerationRequest $request
     * @return CreateStaticContentModerationResponse
     * @throws InvalidStatusCodeException
     * @throws ValidationException
     */
    public function createStaticContentModeration(CreateStaticContentModerationRequest $request): CreateStaticContentModerationResponse
    {
        $response = $this->transport->post(
            self::ENDPOINT_CREATE_STATIC_CONTENT_MODERATION,
            $request->toArray(),
            [
                'Authorization' => $this->hmac->generate($request->toArray(), true),
            ],
            [201]
        );

        return new CreateStaticContentModerationResponse(json_decode($response->getBody()->getContents(), true));
    }

    /**
     * @param string $id
     * @return GetStaticContentModerationResponse
     * @throws InvalidStatusCodeException
     * @throws ValidationException
     */
    public function getStaticContentModeration(string $id): GetStaticContentModerationResponse
    {
        $uri = sprintf(self::ENDPOINT_GET_STATIC_CONTENT_MODERATION, $id);
        $response = $this->transport->get(
            $uri,
            [
                'Authorization' => $this->hmac->generate($uri, true),
            ],
            [200]
        );

        return new GetStaticContentModerationResponse(json_decode($response->getBody()->getContents(), true));
    }

    /**
     * @param string $id
     * @return GetStaticContentModerationParticipantsResponse
     * @throws InvalidStatusCodeException
     * @throws ValidationException
     */
    public function getStaticContentModerationParticipants(string $id): GetStaticContentModerationParticipantsResponse
    {
        $uri = sprintf(self::ENDPOINT_GET_STATIC_CONTENT_MODERATION_PARTICIPANTS, $id);
        $response = $this->transport->get(
            $uri,
            [
                'Authorization' => $this->hmac->generate($uri, true),
            ],
            [200]
        );

        return new GetStaticContentModerationParticipantsResponse(json_decode($response->getBody()->getContents(), true));
    }
}
