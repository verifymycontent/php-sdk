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

    public function __construct($apiKey, $apiSecret)
    {
        $this->hmac = new HMAC($apiKey, $apiSecret);
        $this->identityVerificationClient = new (IdentityVerificationClient::API_VERSIONS[IdentityVerificationClient::API_VERSION_V1])($this->hmac);
    }


    /**
     * @return IdentityVerificationClient
     */
    public function identityVerification(): IdentityVerificationClient
    {
        return $this->identityVerificationClient;
    }

    /**
     * @param string|IdentityVerificationClient $client
     * @return void
     */
    public function setIdentityVerificationClient($client): void
    {
        if (is_string($client)) {
            if (!array_key_exists($client, IdentityVerificationClient::API_VERSIONS)) {
                throw new InvalidArgumentException(
                    'Invalid client. Please use one of the following: ' .
                    implode(', ', array_keys(IdentityVerificationClient::API_VERSIONS))
                );
            }

            $this->identityVerificationClient = new (IdentityVerificationClient::API_VERSIONS[$client])($this->hmac);
            return;
        }

        if (!($client instanceof IdentityVerificationClient)) {
            throw new InvalidArgumentException(
                'Invalid client. Please use one of the following: ' .
                implode(', ', array_keys(IdentityVerificationClient::API_VERSIONS))
            );
        }

        $this->identityVerificationClient = $client;
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
