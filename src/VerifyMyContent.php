<?php

namespace VerifyMyContent\SDK;

use Exception;
use InvalidArgumentException;
use VerifyMyContent\Commons\Security\HMAC;
use VerifyMyContent\SDK\Core\ExportableClient;
use VerifyMyContent\SDK\IdentityVerification\IdentityVerificationClient;

final class VerifyMyContent implements ExportableClient
{
    /**
     * @var IdentityVerificationClient $identityVerificationClient
     */
    private $identityVerificationClient;

    /**
     * @var HMAC $hmac
     */
    private $hmac;

    public function __construct($apiKey, $apiSecret){
        $this->hmac = new HMAC($apiKey, $apiSecret);
        $this->identityVerificationClient = new (IdentityVerificationClient::IDENTITY_VERIFICATION_API_VERSIONS[IdentityVerificationClient::IDENTITY_VERIFICATION_API_VERSION_V1])($this->hmac);
    }


    /**
     * @return IdentityVerificationClient
     */
    public function identityVerification(): IdentityVerificationClient
    {
        return $this->identityVerificationClient;
    }

    public function setIdentityVerificationClient(string $client): void
    {
        if (!array_key_exists($client, IdentityVerificationClient::IDENTITY_VERIFICATION_API_VERSIONS)) {
            throw new InvalidArgumentException('Invalid client');
        }

        $this->identityVerificationClient = new (IdentityVerificationClient::IDENTITY_VERIFICATION_API_VERSIONS[$client])($this->hmac);
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
    }
}
