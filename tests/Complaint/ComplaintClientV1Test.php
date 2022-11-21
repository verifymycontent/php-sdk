<?php

namespace Complaint;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use VerifyMyContent\Commons\Security\HMAC;
use VerifyMyContent\Commons\Transport\HTTP;
use VerifyMyContent\Commons\Transport\InvalidStatusCodeException;
use VerifyMyContent\SDK\Complaint\ComplaintClient;
use VerifyMyContent\SDK\Complaint\ComplaintClientV1;
use VerifyMyContent\SDK\Complaint\Entity\Requests\CreateConsentComplaintRequest;
use VerifyMyContent\SDK\Complaint\Entity\Requests\CreateLiveContentComplaintRequest;
use VerifyMyContent\SDK\Complaint\Entity\Requests\CreateStaticContentComplaintRequest;
use VerifyMyContent\SDK\Core\Validator\ValidationException;

class ComplaintClientV1Test extends TestCase
{
    /**
     * @var HMAC $hmac
     */
    private $hmac;

    public function testSetBaseURL()
    {
        $client = $this->newClient();
        $mockTransport = $this->mockTransport();
        $mockTransport->expects($this->once())
            ->method('setBaseURL')
            ->with('https://example.com');
        $client->setTransport($mockTransport);
        $client->setBaseURL('https://example.com');
    }

    private function newClient(): ComplaintClientV1
    {
        return new ComplaintClientV1($this->hmac);
    }

    private function mockTransport()
    {
        return $this->createMock(HTTP::class);
    }

    public function testUseSandbox()
    {
        $client = $this->newClient();
        $mockTransport = $this->mockTransport();
        $mockTransport->expects($this->once())
            ->method('setBaseURL')
            ->with(ComplaintClient::SANDBOX_URL);
        $client->setTransport($mockTransport);
        $client->useSandbox();
    }

    public function testCreateStaticContentComplaint()
    {
        $input = $this->staticComplaintInput();
        $output = $this->staticComplaintOutput();

        $client = $this->newClient();
        $mockTransport = $this->mockTransport();
        $mockTransport->expects($this->once())
            ->method('post')
            ->with(ComplaintClientV1::CREATE_STATIC_CONTENT_COMPLAINT_ENDPOINT, $input)
            ->willReturn($this->mockResponse($output, 201));

        $client->setTransport($mockTransport);
        $response = $client->createStaticContentComplaint(new CreateStaticContentComplaintRequest($input));
        $this->assertEquals($output['id'], $response->id);
        $this->assertEquals($output['status'], $response->status);
        $this->assertEquals($output['created_at'], $response->created_at->format('Y-m-d H:i:s'));
        $this->assertEquals($output['updated_at'], $response->updated_at->format('Y-m-d H:i:s'));
        $this->assertEquals($output['notes'], $response->notes);
        $this->assertEquals($output['tags'], $response->tags);
        $this->assertEquals($output['external_id'], $response->external_id);
    }

    private function staticComplaintInput(): array
    {
        return [
            "content" => [
                "description" => "Your description",
                "external_id" => "YOUR-VIDEO-ID",
                "tags" => [
                    "VIOLATION_1"
                ],
                "title" => "Your title",
                "type" => "video",
                "url" => "https://example.com/video.mp4"
            ],
            "customer" => [
                "id" => "YOUR-USER-ID"
            ],
            "webhook" => "https://example.com/webhook"
        ];
    }

    private function staticComplaintOutput(): array
    {
        return [
            "external_id" => "YOUR-CORRELATION-ID",
            "id" => "ABC-123-5678-ABC",
            "notes" => "Harmful content found.",
            "status" => "Confirmed",
            "tags" => [
                "UNDERAGE"
            ],
            "created_at" => "2020-11-12 19:06:00",
            "updated_at" => "2020-11-12 19:06:00"
        ];
    }

    private function mockResponse(array $body, int $status = 200): ResponseInterface
    {
        return $this->createConfiguredMock(ResponseInterface::class, [
            'getStatusCode' => $status,
            'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                'getContents' => json_encode($body)
            ])
        ]);
    }

    public function testCreateStaticContentComplaintShouldThrowIfInvalidStatusException()
    {
        $this->expectException(InvalidStatusCodeException::class);
        $input = $this->staticComplaintInput();

        $client = $this->newClient();
        $mockTransport = $this->mockTransport();
        $mockTransport->expects($this->once())
            ->method('post')
            ->with(ComplaintClientV1::CREATE_STATIC_CONTENT_COMPLAINT_ENDPOINT, $input)
            ->willThrowException(new InvalidStatusCodeException(400));

        $client->setTransport($mockTransport);
        $client->createStaticContentComplaint(new CreateStaticContentComplaintRequest($input));
    }

    public function testCreateStaticContentComplaintInvalidJson()
    {
        $this->expectException(ValidationException::class);
        $input = $this->staticComplaintInput();

        $client = $this->newClient();
        $mockTransport = $this->mockTransport();
        $mockTransport->expects($this->once())
            ->method('post')
            ->with(ComplaintClientV1::CREATE_STATIC_CONTENT_COMPLAINT_ENDPOINT, $input)
            ->willReturn($this->mockResponse([], 201));

        $client->setTransport($mockTransport);
        $client->createStaticContentComplaint(new CreateStaticContentComplaintRequest($input));
    }

    public function testCreateConsentComplaint()
    {
        $input = $this->consentComplaintInput();
        $output = $this->consentComplaintOutput();

        $client = $this->newClient();
        $mockTransport = $this->mockTransport();
        $mockTransport->expects($this->once())
            ->method('post')
            ->with(ComplaintClientV1::CREATE_CONSENT_COMPLAINT_ENDPOINT, $input)
            ->willReturn($this->mockResponse($output, 201));

        $client->setTransport($mockTransport);
        $response = $client->createConsentComplaint(new CreateConsentComplaintRequest($input));
        $this->assertEquals($output['id'], $response->id);
        $this->assertEquals($output['status'], $response->status);
        $this->assertEquals($output['redirect_url'], $response->redirect_url);
    }

    public function testCreateConsentComplaintShouldThrowIfInvalidStatusException()
    {
        $this->expectException(InvalidStatusCodeException::class);
        $input = $this->consentComplaintInput();

        $client = $this->newClient();
        $mockTransport = $this->mockTransport();
        $mockTransport->expects($this->once())
            ->method('post')
            ->with(ComplaintClientV1::CREATE_CONSENT_COMPLAINT_ENDPOINT, $input)
            ->willThrowException(new InvalidStatusCodeException(400));

        $client->setTransport($mockTransport);
        $client->createConsentComplaint(new CreateConsentComplaintRequest($input));
    }

    public function testCreateConsentComplaintInvalidJson()
    {
        $this->expectException(ValidationException::class);
        $input = $this->consentComplaintInput();

        $client = $this->newClient();
        $mockTransport = $this->mockTransport();
        $mockTransport->expects($this->once())
            ->method('post')
            ->with(ComplaintClientV1::CREATE_CONSENT_COMPLAINT_ENDPOINT, $input)
            ->willReturn($this->mockResponse([], 201));

        $client->setTransport($mockTransport);
        $client->createConsentComplaint(new CreateConsentComplaintRequest($input));
    }

    public function testCreateLiveContentComplaint()
    {
        $input = $this->liveComplaintInput();
        $output = $this->liveComplaintOutput();

        $client = $this->newClient();
        $mockTransport = $this->mockTransport();
        $mockTransport->expects($this->once())
            ->method('post')
            ->with(ComplaintClientV1::CREATE_LIVE_CONTENT_COMPLAINT_ENDPOINT, $input)
            ->willReturn($this->mockResponse($output, 201));
        $client->setTransport($mockTransport);

        $response = $client->createLiveContentComplaint(new CreateLiveContentComplaintRequest($input));
        $this->assertEquals($output['id'], $response->id);
        $this->assertEquals($output['status'], $response->status);
        $this->assertEquals($output['created_at'], $response->created_at->format('Y-m-d H:i:s'));
        $this->assertEquals($output['updated_at'], $response->updated_at->format('Y-m-d H:i:s'));
        $this->assertEquals($output['notes'], $response->notes);
        $this->assertEquals($output['tags'], $response->tags);
        $this->assertEquals($output['external_id'], $response->external_id);
    }

    private function liveComplaintInput(): array
    {
        return [
            "complained_at" => "2022-11-04T12:04:08.658Z",
            "customer" => [
                "id" => "YOUR-USER-ID"
            ],
            "stream" => [
                "external_id" => "YOUR-LIVESTREAM-ID",
                "tags" => [
                    "VIOLATION_1"
                ]
            ],
            "webhook" => "https://example.com/webhook"
        ];
    }

    private function liveComplaintOutput(): array
    {
        return [
            "external_id" => "YOUR-CORRELATION-ID",
            "id" => "ABC-123-5678-ABC",
            "notes" => "Harmful content found.",
            "status" => "Confirmed",
            "tags" => [
                "UNDERAGE"
            ],
            "created_at" => "2020-11-12 19:06:00",
            "updated_at" => "2020-11-12 19:06:00"
        ];
    }

    public function testCreateLiveContentComplaintShouldThrowIfInvalidStatusException()
    {
        $this->expectException(InvalidStatusCodeException::class);
        $input = $this->liveComplaintInput();

        $client = $this->newClient();
        $mockTransport = $this->mockTransport();
        $mockTransport->expects($this->once())
            ->method('post')
            ->with(ComplaintClientV1::CREATE_LIVE_CONTENT_COMPLAINT_ENDPOINT, $input)
            ->willThrowException(new InvalidStatusCodeException(400));

        $client->setTransport($mockTransport);
        $client->createLiveContentComplaint(new CreateLiveContentComplaintRequest($input));
    }

    public function testCreateLiveContentComplaintInvalidJson()
    {
        $this->expectException(ValidationException::class);
        $input = $this->liveComplaintInput();

        $client = $this->newClient();
        $mockTransport = $this->mockTransport();
        $mockTransport->expects($this->once())
            ->method('post')
            ->with(ComplaintClientV1::CREATE_LIVE_CONTENT_COMPLAINT_ENDPOINT, $input)
            ->willReturn($this->mockResponse([], 201));

        $client->setTransport($mockTransport);
        $client->createLiveContentComplaint(new CreateLiveContentComplaintRequest($input));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->hmac = new HMAC('api-key', 'api-secret');
    }


    private function consentComplaintInput(): array
    {
        return [
            "content" => [
                "external_id" => "string"
            ],
            "customer" => [
                "id" => "string"
            ],
            "webhook" => "https://example.com/webhook",
        ];
    }

    private function consentComplaintOutput(): array
    {
        return [
            "id" => "string",
            "redirect_url" => "https://example.com/redirect",
            "status" => "string"
        ];
    }
}
