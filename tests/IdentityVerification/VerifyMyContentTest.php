<?php


use VerifyMyContent\Commons\Security\HMAC;
use VerifyMyContent\SDK\IdentityVerification\IdentityVerificationClient;
use VerifyMyContent\SDK\VerifyMyContent;
use PHPUnit\Framework\TestCase;

class VerifyMyContentTest extends TestCase
{
    /**
     * @var HMAC $hmac
     */
    private $hmac;

    public function __construct()
    {
        parent::__construct();
        $this->hmac = new HMAC('api-key', 'api-secret');
    }

    private function newVmc(): VerifyMyContent
    {
        return new VerifyMyContent('api-key', 'api-secret');
    }

    public function testShouldSetIdentityVerificationClient()
    {
        $client = $this->newVmc();
        $this->assertInstanceOf(VerifyMyContent::class, $client);

        $mockIdentityVerificationClient = $this->createMock(IdentityVerificationClient::class);
        $client->setIdentityVerificationClient($mockIdentityVerificationClient);

        $this->assertEquals($mockIdentityVerificationClient, $client->identityVerification());

        $v1ClientClassName = IdentityVerificationClient::API_VERSIONS[IdentityVerificationClient::API_VERSION_V1];
        $v1Client = new $v1ClientClassName($this->hmac);
        $client->setIdentityVerificationClient($v1Client);
        $this->assertEquals($v1Client, $client->identityVerification());

        $v1ClientIndex = IdentityVerificationClient::API_VERSION_V1;
        $client->setIdentityVerificationClient($v1ClientIndex);
        $this->assertInstanceOf($v1ClientClassName, $client->identityVerification());
    }

    public function testShouldSetIdentityVerificationClientShouldThrowIfInvalidClientKey()
    {
        $this->expectException(InvalidArgumentException::class);
        $client = $this->newVmc();
        $client->setIdentityVerificationClient('invalid-client');
    }

    public function testShouldSetIdentityVerificationClientShouldThrowIfInvalidClient()
    {
        $this->expectException(InvalidArgumentException::class);
        $client = $this->newVmc();
        $client->setIdentityVerificationClient(new stdClass());
    }

    public function testSetBaseURLShouldThrowWithAnyCall()
    {
        $client = $this->newVmc();

        $this->expectException(Exception::class);
        $client->setBaseURL('https://example.com');
    }

    public function testSetBaseURLShouldThrowWithAnyCall2(){
        $client = $this->newVmc();

        $this->expectException(Exception::class);
        $client->setBaseURL('');
    }

    public function testUseSandboxShouldCallProvidersUseSandbox()
    {
        $client = $this->newVmc();

        $mockIdentityVerificationClient = $this->createMock(IdentityVerificationClient::class);
        $mockIdentityVerificationClient->expects($this->once())
            ->method('useSandbox');
        $client->setIdentityVerificationClient($mockIdentityVerificationClient);
        $client->useSandbox();
    }
}
