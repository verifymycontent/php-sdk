<?php

namespace VerifyMyContent\SDK\IdentityVerification;

use VerifyMy\SDK\Business\Entity\Requests\AllowedRedirectUrlsRequest;
use VerifyMyContent\Commons\Security\HMAC;
use VerifyMyContent\Commons\Transport\HTTP;
use VerifyMyContent\Commons\Transport\InvalidStatusCodeException;
use VerifyMyContent\SDK\Core\Validator\ValidationException;
use VerifyMyContent\SDK\IdentityVerification\Entity\Requests\CreateIdentityVerificationRequest;
use VerifyMyContent\SDK\IdentityVerification\Entity\Responses\CreateIdentityVerificationResponse;
use VerifyMyContent\SDK\IdentityVerification\Entity\Responses\GetIdentityVerificationResponse;
use VerifyMy\SDK\VerifyMy;

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

    /**
     * @var VerifyMy
     */

    private $verifyMy;

    /**
     * @var string
     */
    private $apiKey;

    public function __construct(HMAC $hmac, string $apiKey)
    {
        $this->hmac = $hmac;
        $this->transport = new HTTP(IdentityVerificationClient::PRODUCTION_URL);
        $this->verifyMy = new VerifyMy(IdentityVerificationClient::PRODUCTION_URL, $apiKey);
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
     * @param string $id
     * @return GetIdentityVerificationResponse
     * @throws InvalidStatusCodeException
     * @throws ValidationException
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

    /**
     * @param array $urls
     * @return void
     * @throws InvalidStatusCodeException
     * @throws ValidationException
     */
    public function addRedirectUrls(array $urls): void
    {
        $this->verifyMy->business()->addAllowedRedirectUrls(new AllowedRedirectUrlsRequest(
            ["urls" => $urls]
        ));
    }

    /**
     * @param array $urls
     * @return void
     * @throws InvalidStatusCodeException
     * @throws ValidationException
     */
    public function removeRedirectUrls(array $urls): void
    {
        $this->verifyMy->business()->removeAllowedRedirectUrls(new AllowedRedirectUrlsRequest(
            ["urls" => $urls]
        ));
    }

    public function useSandbox(): void
    {
        $this->setBaseURL(IdentityVerificationClient::SANDBOX_URL);
        $this->verifyMy = new VerifyMy(IdentityVerificationClient::SANDBOX_URL, $this->apiKey);
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
