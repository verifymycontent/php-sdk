<?php

namespace VerifyMyContent\SDK\ContentModeration;

use VerifyMyContent\Commons\Security\HMAC;
use VerifyMyContent\Commons\Transport\HTTP;
use VerifyMyContent\Commons\Transport\InvalidStatusCodeException;
use VerifyMyContent\SDK\ContentModeration\Entity\Requests\CreateAnonymousLiveContentModerationRequest;
use VerifyMyContent\SDK\ContentModeration\Entity\Requests\CreateLiveContentModerationRequest;
use VerifyMyContent\SDK\ContentModeration\Entity\Requests\CreateStaticContentModerationRequest;
use VerifyMyContent\SDK\ContentModeration\Entity\Requests\StartLiveContentModerationRequest;
use VerifyMyContent\SDK\ContentModeration\Entity\Requests\ChangeLiveContentRuleRequest;
use VerifyMyContent\SDK\ContentModeration\Entity\Responses\CreateLiveContentModerationResponse;
use VerifyMyContent\SDK\ContentModeration\Entity\Responses\CreateStaticContentModerationResponse;
use VerifyMyContent\SDK\ContentModeration\Entity\Responses\GetLiveContentModerationResponse;
use VerifyMyContent\SDK\ContentModeration\Entity\Responses\GetStaticContentModerationResponse;
use VerifyMyContent\SDK\Core\Validator\ValidationException;

final class ContentModerationClientV1 implements ContentModerationClient
{
    const ENDPOINT_CREATE_STATIC_CONTENT_MODERATION = '/api/v1/moderation';
    const ENDPOINT_CREATE_STATIC_CONTENT_MODERATION_V2 = '/api/v2/moderation';
    const ENDPOINT_GET_STATIC_CONTENT_MODERATION = '/api/v1/moderation/%s';
    const ENDPOINT_START_LIVE_CONTENT_MODERATION = '/api/v1/livestream/%s/start';
    const ENDPOINT_CREATE_LIVE_CONTENT_MODERATION = '/api/v1/livestream';
    const ENDPOINT_GET_LIVE_CONTENT_MODERATION = '/api/v1/livestream/%s';
    const ENDPOINT_CREATE_ANONYMOUS_LIVE_CONTENT_MODERATION = '/api/v1/livestream-anonymous';
    const ENDPOINT_CHANGE_LIVE_CONTENT_RULE = '/api/v1/livestream/%s/rule';

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
     * @return void
     * @throws InvalidStatusCodeException
     */
    public function startLiveContentModeration(string $id, StartLiveContentModerationRequest $request): void
    {
        $uri = sprintf(self::ENDPOINT_START_LIVE_CONTENT_MODERATION, $id);
        $this->transport->patch(
            $uri,
            $request->toArray(),
            [
                'Authorization' => $this->hmac->generate($uri, true),
            ],
            [204]
        );
    }

    /**
     * @param CreateLiveContentModerationRequest $request
     * @return CreateLiveContentModerationResponse
     * @throws InvalidStatusCodeException
     * @throws ValidationException
     */
    public function createLiveContentModeration(CreateLiveContentModerationRequest $request): CreateLiveContentModerationResponse
    {
        $response = $this->transport->post(
            self::ENDPOINT_CREATE_LIVE_CONTENT_MODERATION,
            $request->toArray(),
            [
                'Authorization' => $this->hmac->generate($request->toArray(), true),
            ],
            [201]
        );

        return new CreateLiveContentModerationResponse(json_decode($response->getBody()->getContents(), true));
    }

    /**
     * @param string $id
     * @return GetLiveContentModerationResponse
     * @throws InvalidStatusCodeException
     * @throws ValidationException
     */
    public function getLiveContentModeration(string $id): GetLiveContentModerationResponse
    {
        $uri = sprintf(self::ENDPOINT_GET_LIVE_CONTENT_MODERATION, $id);
        $response = $this->transport->get(
            $uri,
            [
                'Authorization' => $this->hmac->generate($uri, true),
            ],
            [200]
        );

        return new GetLiveContentModerationResponse(json_decode($response->getBody()->getContents(), true));
    }

    /**
     * @param CreateAnonymousLiveContentModerationRequest $request
     * @return CreateLiveContentModerationResponse
     * @throws InvalidStatusCodeException
     * @throws ValidationException
     */
    public function createAnonymousLiveContentModeration(CreateAnonymousLiveContentModerationRequest $request): CreateLiveContentModerationResponse
    {
        $response = $this->transport->post(
            self::ENDPOINT_CREATE_ANONYMOUS_LIVE_CONTENT_MODERATION,
            $request->toArray(),
            [
                'Authorization' => $this->hmac->generate($request->toArray(), true),
            ],
            [201]
        );

        return new CreateLiveContentModerationResponse(json_decode($response->getBody()->getContents(), true));
    }

    /**
     * @param string $id
     * @param ChangeLiveContentRuleRequest $request
     * @return void
     * @throws InvalidStatusCodeException
     */
    public function changeLiveContentRule(string $id, ChangeLiveContentRuleRequest $request): void
    {
        $uri = sprintf(self::ENDPOINT_CHANGE_LIVE_CONTENT_RULE, $id);
        $this->transport->patch(
            $uri,
            $request->toArray(),
            [
                'Authorization' => $this->hmac->generate($request->toArray(), true),
            ],
            [204]
        );
    }
}
