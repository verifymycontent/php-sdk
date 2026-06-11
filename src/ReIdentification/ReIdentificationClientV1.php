<?php

namespace VerifyMyContent\SDK\ReIdentification;

use VerifyMyContent\Commons\Security\HMAC;
use VerifyMyContent\Commons\Transport\HTTP;
use VerifyMyContent\Commons\Transport\InvalidStatusCodeException;
use VerifyMyContent\SDK\ReIdentification\Entity\Requests\CreateReIdentificationRequest;
use VerifyMyContent\SDK\ReIdentification\Entity\Responses\CreateReIdentificationResponse;
use VerifyMyContent\SDK\ReIdentification\Entity\Responses\GetReIdentificationResponse;
use VerifyMyContent\SDK\ReIdentification\Exception\FeatureNotEnabledException;
use VerifyMyContent\SDK\ReIdentification\Exception\NoApprovedVerificationFoundException;

final class ReIdentificationClientV1 implements ReIdentificationClient
{
    const ENDPOINT_CREATE_RE_IDENTIFICATION = '/api/v1/re-identification';
    const ENDPOINT_GET_RE_IDENTIFICATION = '/api/v1/re-identification/%s';

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
        $this->transport = new HTTP(ReIdentificationClient::PRODUCTION_URL);
    }

    /**
     * @throws InvalidStatusCodeException
     * @throws FeatureNotEnabledException
     * @throws NoApprovedVerificationFoundException
     */
    public function createReIdentification(CreateReIdentificationRequest $request): CreateReIdentificationResponse
    {
        $response = $this->transport->post(
            self::ENDPOINT_CREATE_RE_IDENTIFICATION,
            $request->toArray(),
            [
                'Authorization' => $this->sign($request->toArray()),
            ],
            [200, 201, 403, 404]
        );

        if ($response->getStatusCode() === 403) {
            throw new FeatureNotEnabledException();
        }

        if ($response->getStatusCode() === 404) {
            throw new NoApprovedVerificationFoundException();
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return new CreateReIdentificationResponse($data);
    }

    /**
     * @throws InvalidStatusCodeException
     */
    public function getReIdentification(string $id): GetReIdentificationResponse
    {
        $uri = sprintf(self::ENDPOINT_GET_RE_IDENTIFICATION, $id);
        $response = $this->transport->get(
            $uri,
            [
                'Authorization' => $this->sign($uri),
            ],
            [200]
        );

        $data = json_decode($response->getBody()->getContents(), true);
        return new GetReIdentificationResponse($data);
    }

    public function useSandbox(): void
    {
        $this->setBaseURL(ReIdentificationClient::SANDBOX_URL);
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
