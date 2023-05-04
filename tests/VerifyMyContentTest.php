<?php


use PHPUnit\Framework\TestCase;
use VerifyMyContent\Commons\Security\HMAC;
use VerifyMyContent\SDK\Complaint\ComplaintClient;
use VerifyMyContent\SDK\ContentModeration\ContentModerationClient;
use VerifyMyContent\SDK\IdentityVerification\IdentityVerificationClient;
use VerifyMyContent\SDK\VerifyMyContent;

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

    public function testShouldSetContentModerationClient()
    {
        $client = $this->newVmc();
        $this->assertInstanceOf(VerifyMyContent::class, $client);

        $mockContentModerationClient = $this->createMock(ContentModerationClient::class);
        $client->setContentModerationClient($mockContentModerationClient);

        $this->assertEquals($mockContentModerationClient, $client->contentModeration());

        $v1ClientClassName = ContentModerationClient::API_VERSIONS[ContentModerationClient::API_VERSION_V1];
        $v1Client = new $v1ClientClassName($this->hmac);
        $client->setContentModerationClient($v1Client);
        $this->assertEquals($v1Client, $client->contentModeration());

        $v1ClientIndex = ContentModerationClient::API_VERSION_V1;
        $client->setContentModerationClient($v1ClientIndex);
        $this->assertInstanceOf($v1ClientClassName, $client->contentModeration());
    }

    public function testShouldSetContentModerationClientShouldThrowIfInvalidClientKey()
    {
        $this->expectException(InvalidArgumentException::class);
        $client = $this->newVmc();
        $client->setContentModerationClient('invalid-client');
    }

    public function testShouldSetContentModerationClientShouldThrowIfInvalidClient()
    {
        $this->expectException(InvalidArgumentException::class);
        $client = $this->newVmc();
        $client->setContentModerationClient(new stdClass());
    }

    public function testShouldSetComplaintClient()
    {
        $client = $this->newVmc();
        $this->assertInstanceOf(VerifyMyContent::class, $client);

        $mockComplaintClient = $this->createMock(ComplaintClient::class);
        $client->setComplaintClient($mockComplaintClient);

        $this->assertEquals($mockComplaintClient, $client->complaint());

        $v1ClientClassName = ComplaintClient::API_VERSIONS[ComplaintClient::API_VERSION_V1];
        $v1Client = new $v1ClientClassName($this->hmac);
        $client->setComplaintClient($v1Client);
        $this->assertEquals($v1Client, $client->complaint());

        $v1ClientIndex = ComplaintClient::API_VERSION_V1;
        $client->setComplaintClient($v1ClientIndex);
        $this->assertInstanceOf($v1ClientClassName, $client->complaint());
    }

    public function testShouldSetComplaintClientShouldThrowIfInvalidClientKey()
    {
        $this->expectException(InvalidArgumentException::class);
        $client = $this->newVmc();
        $client->setComplaintClient('invalid-client');
    }

    public function testShouldSetComplaintClientShouldThrowIfInvalidClient()
    {
        $this->expectException(InvalidArgumentException::class);
        $client = $this->newVmc();
        $client->setComplaintClient(new stdClass());
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

        $mockContentModerationClient = $this->createMock(ContentModerationClient::class);
        $mockContentModerationClient->expects($this->once())
            ->method('useSandbox');

        $mockComplaintClient = $this->createMock(ComplaintClient::class);
        $mockComplaintClient->expects($this->once())
            ->method('useSandbox');

        $client->setIdentityVerificationClient($mockIdentityVerificationClient);
        $client->setContentModerationClient($mockContentModerationClient);
        $client->setComplaintClient($mockComplaintClient);

        $client->useSandbox();
    }
}
