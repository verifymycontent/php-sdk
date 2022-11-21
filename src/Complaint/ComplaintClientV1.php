<?php

namespace VerifyMyContent\SDK\Complaint;

use VerifyMyContent\Commons\Security\HMAC;
use VerifyMyContent\Commons\Transport\HTTP;
use VerifyMyContent\SDK\Complaint\Entity\Requests\CreateConsentComplaintRequest;
use VerifyMyContent\SDK\Complaint\Entity\Requests\CreateLiveContentComplaintRequest;
use VerifyMyContent\SDK\Complaint\Entity\Requests\CreateStaticContentComplaintRequest;
use VerifyMyContent\SDK\Complaint\Entity\Responses\CreateConsentComplaintResponse;
use VerifyMyContent\SDK\Complaint\Entity\Responses\CreateLiveContentComplaintResponse;
use VerifyMyContent\SDK\Complaint\Entity\Responses\CreateStaticContentComplaintResponse;

class ComplaintClientV1 implements ComplaintClient
{
    const CREATE_STATIC_CONTENT_COMPLAINT_ENDPOINT = '/api/v1/complaint-moderation';
    const CREATE_LIVE_CONTENT_COMPLAINT_ENDPOINT = '/api/v1/complaint-livestream';
    const CREATE_CONSENT_COMPLAINT_ENDPOINT = '/api/v1/complaint-consent';

    /**
     * @var HTTP $transport
     */
    private $transport;

    /**
     * @var HMAC $hmac
     */
    private HMAC $hmac;

    /**
     * @param HMAC $hmac
     */
    public function __construct(HMAC $hmac)
    {
        $this->hmac = $hmac;
        $this->transport = new HTTP(ComplaintClient::PRODUCTION_URL);
    }

    public function createConsentComplaint(CreateConsentComplaintRequest $request): CreateConsentComplaintResponse
    {
        $response = $this->transport->post(
            self::CREATE_CONSENT_COMPLAINT_ENDPOINT,
            $request->toArray(),
            ['Authorization' => $this->hmac->generate($request->toArray(), true)],
            [201]
        );

        return new CreateConsentComplaintResponse(json_decode($response->getBody()->getContents(), true));
    }

    public function createStaticContentComplaint(CreateStaticContentComplaintRequest $request): CreateStaticContentComplaintResponse
    {
        $response = $this->transport->post(
            self::CREATE_STATIC_CONTENT_COMPLAINT_ENDPOINT,
            $request->toArray(),
            ['Authorization' => $this->hmac->generate($request->toArray(), true)],
            [201]
        );

        return new CreateStaticContentComplaintResponse(json_decode($response->getBody()->getContents(), true));
    }

    public function createLiveContentComplaint(CreateLiveContentComplaintRequest $request): CreateLiveContentComplaintResponse
    {
        $response = $this->transport->post(
            self::CREATE_LIVE_CONTENT_COMPLAINT_ENDPOINT,
            $request->toArray(),
            ['Authorization' => $this->hmac->generate($request->toArray(), true)],
            [201]
        );

        return new CreateLiveContentComplaintResponse(json_decode($response->getBody()->getContents(), true));
    }

    public function useSandbox(): void
    {
        $this->transport->setBaseUrl(ComplaintClient::SANDBOX_URL);
    }

    public function setBaseURL(string $baseURL): void
    {
        $this->transport->setBaseUrl($baseURL);
    }

    public function setTransport(HTTP $transport): void
    {
        $this->transport = $transport;
    }
}
