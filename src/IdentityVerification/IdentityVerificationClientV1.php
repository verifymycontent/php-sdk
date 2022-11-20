<?php

namespace VerifyMyContent\SDK\IdentityVerification;

use VerifyMyContent\Commons\Security\HMAC;
use VerifyMyContent\Commons\Transport\HTTP;
use VerifyMyContent\Commons\Transport\InvalidStatusCodeException;
use VerifyMyContent\SDK\IdentityVerification\Entity\Requests\CreateIdentityVerificationRequest;
use VerifyMyContent\SDK\IdentityVerification\Entity\Responses\CreateIdentityVerificationResponse;
use VerifyMyContent\SDK\IdentityVerification\Entity\Responses\GetIdentityVerificationResponse;

final class IdentityVerificationClientV1 implements IdentityVerificationClient
{
    const ENDPOINT_CREATE_IDENTITY_CHECK = '/api/v1/identity-verification';
    const ENDPOINT_GET_IDENTITY_CHECK = '/api/v1/identity-verification/%s';

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
        $this->transport = new HTTP(IdentityVerificationClient::PRODUCTION_URL);
    }

    /**
     * @throws InvalidStatusCodeException
     */
    public function createIdentityVerification(CreateIdentityVerificationRequest $request): CreateIdentityVerificationResponse
    {
        $response = $this->transport->post(
            self::ENDPOINT_CREATE_IDENTITY_CHECK,
            $request->toArray(),
            [
                'Authorization' => $this->sign($request->toArray()),
            ],
            [201]
        );

        $data = json_decode($response->getBody()->getContents(), true);
        return new CreateIdentityVerificationResponse($data);
    }

    /**
     * @throws InvalidStatusCodeException
     */
    public function getIdentityVerification(string $id): GetIdentityVerificationResponse
    {
        $uri = sprintf(self::ENDPOINT_GET_IDENTITY_CHECK, $id);
        $response = $this->transport->get(
            $uri,
            [
                'Authorization' => $this->sign($uri),
            ],
            [200]
        );

        $data = json_decode($response->getBody()->getContents(), true);
        return new GetIdentityVerificationResponse($data);
    }

    public function useSandbox(): void
    {
        $this->setBaseURL(IdentityVerificationClient::SANDBOX_URL);
    }

    public function setBaseURL(string $baseURL): void
    {
        $this->transport->setBaseUrl($baseURL);
    }

    private function sign($input): string
    {
        return sprintf("hmac %s", $this->hmac->generate($input));
    }

    /**
     * @param HTTP $transport
     */
    public function setTransport(HTTP $transport): void
    {
        $this->transport = $transport;
    }

}
