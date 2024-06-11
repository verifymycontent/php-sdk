<?php

namespace VerifyMyContent\SDK;

use Exception;
use InvalidArgumentException;
use VerifyMy\SDK\VerifyMy;
use VerifyMyContent\Commons\Security\HMAC;
use VerifyMyContent\SDK\Complaint\ComplaintClient;
use VerifyMyContent\SDK\ContentModeration\ContentModerationClient;
use VerifyMyContent\SDK\IdentityVerification\IdentityVerificationClient;
use VerifyMy\SDK\Business\Entity\Requests\AllowedRedirectUrlsRequest;

final class VerifyMyContent implements VerifyMyContentInterface
{
    /**
     * @var IdentityVerificationClient $identityVerificationClient
     */
    private $identityVerificationClient;

    /**
     * @var HMAC $hmac
     */
    private $hmac;

    /**
     * @var ContentModerationClient $contentModerationClient
     */
    private $contentModerationClient;

    /**
     * @var ComplaintClient $complaintClient
     */
    private $complaintClient;

    /**
     * @var VerifyMy $verifyMy
     */
    private $verifyMy;

    /**
     * @var string $apiKey
     */
    private $apiKey;
    /**
     * @var string
     */
    private $apiSecret;

    public function __construct($apiKey, $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->hmac = new HMAC($apiKey, $apiSecret);

        $identityVerificationClientClassName = IdentityVerificationClient::API_VERSIONS[IdentityVerificationClient::API_VERSION_V1];
        $this->identityVerificationClient = new $identityVerificationClientClassName($this->hmac);

        $contentModerationClientClassName = ContentModerationClient::API_VERSIONS[ContentModerationClient::API_VERSION_V1];
        $this->contentModerationClient = new $contentModerationClientClassName($this->hmac);

        $consentComplaintClientClassName = ComplaintClient::API_VERSIONS[ComplaintClient::API_VERSION_V1];
        $this->complaintClient = new $consentComplaintClientClassName($this->hmac);

        $this->verifyMy = new VerifyMy(IdentityVerificationClient::PRODUCTION_URL, $apiKey, $apiSecret);
    }

    /**
     * @param array $urls
     * @return void
     */
    public function addRedirectUrls(array $urls):void
    {
        $this->verifyMy->business()->addAllowedRedirectUrls(new AllowedRedirectUrlsRequest(
            ["urls" => $urls]
        ));
    }

    /**
     * @param array $urls
     * @return void
     */
    public function removeRedirectUrls(array $urls):void
    {
        $this->verifyMy->business()->removeAllowedRedirectUrls(new AllowedRedirectUrlsRequest(
            ["urls" => $urls]
        ));
    }

    /**
     * @return IdentityVerificationClient
     */
    public function identityVerification(): IdentityVerificationClient
    {
        return $this->identityVerificationClient;
    }

    public function contentModeration(): ContentModerationClient
    {
        return $this->contentModerationClient;
    }

    public function complaint(): ComplaintClient
    {
        return $this->complaintClient;
    }


    private function setClient($client, $clientAttribute, $clientClass, $clientVersions): void
    {
        if (is_string($client)) {
            if (!array_key_exists($client, $clientVersions)) {
                throw new InvalidArgumentException(
                    'Invalid client. Please use one of the following: ' .
                    implode(', ', array_keys($clientVersions))
                );
            }

            $clientClassName = $clientVersions[$client];
            $this->$clientAttribute = new $clientClassName($this->hmac);
            return;
        }

        if (!($client instanceof $clientClass)) {
            throw new InvalidArgumentException(
                'Invalid client. Please use one of the following: ' .
                implode(', ', array_keys($clientVersions))
            );
        }

        $this->$clientAttribute = $client;
    }

    /**
     * @param string|IdentityVerificationClient $client
     * @return void
     */
    public function setIdentityVerificationClient($client): void
    {
        $this->setClient($client, 'identityVerificationClient', IdentityVerificationClient::class, IdentityVerificationClient::API_VERSIONS);
    }

    /**
     * @param string|ContentModerationClient $client
     * @return void
     */
    public function setContentModerationClient($client): void
    {
        $this->setClient($client, 'contentModerationClient', ContentModerationClient::class, ContentModerationClient::API_VERSIONS);
    }

    /**
     * @param string|ComplaintClient $client
     * @return void
     */
    public function setComplaintClient($client): void
    {
        $this->setClient($client, 'complaintClient', ComplaintClient::class, ComplaintClient::API_VERSIONS);
    }


    /**
     * @throws Exception
     * @deprecated Should not call on VerifyMyContent.
     * Instead call on the client you want to set the base URL for.
     * Eg. $vmc->identityVerification()->setBaseURL('https://other.url')
     */
    public function setBaseURL(string $baseURL): void
    {
        throw new Exception("Should not call on VerifyMyContent. " .
            "Instead call on the client you want to set the base URL for. " .
            "Eg. \$vmc->identityVerification()->setBaseURL('https://other.url')");
    }


    public function useSandbox(): void
    {
        $this->identityVerificationClient->useSandbox();
        $this->contentModerationClient->useSandbox();
        $this->complaintClient->useSandbox();
        $this->verifyMy = new VerifyMy(IdentityVerificationClient::SANDBOX_URL, $this->apiKey, $this->apiSecret);
    }
}
