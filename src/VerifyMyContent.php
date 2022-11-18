<?php

namespace VerifyMyContent\SDK;

use Exception;
use InvalidArgumentException;
use VerifyMyContent\Commons\Security\HMAC;
use VerifyMyContent\SDK\Core\ExportableClient;
use VerifyMyContent\SDK\IdentityVerification\IdentityVerificationClient;
use VerifyMyContent\SDK\IdentityVerification\IdentityVerificationClientV1;

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

        $this->identityVerificationClient = new IdentityVerificationClientV1($this->hmac);
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
        if (!class_exists($client)) {
            throw new InvalidArgumentException("Class {$client} does not exist");
        }

        $this->identityVerificationClient = new $client($this->hmac);
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
