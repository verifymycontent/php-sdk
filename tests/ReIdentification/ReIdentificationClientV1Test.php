<?php

namespace VerifyMyContent\SDK\ReIdentification;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use VerifyMyContent\Commons\Security\HMAC;
use VerifyMyContent\Commons\Transport\HTTP;
use VerifyMyContent\Commons\Transport\InvalidStatusCodeException;
use VerifyMyContent\SDK\Core\Validator\ValidationException;
use VerifyMyContent\SDK\ReIdentification\Entity\Requests\CreateReIdentificationRequest;
use VerifyMyContent\SDK\ReIdentification\Exception\FeatureNotEnabledException;
use VerifyMyContent\SDK\ReIdentification\Exception\NoApprovedVerificationFoundException;

class ReIdentificationClientV1Test extends TestCase
{
    /**
     * @var HMAC $hmac
     */
    private $hmac;

    public function testCreateReIdentification()
    {
        $input = $this->createReIdentificationInput();
        $transportMock = $this->createMock(HTTP::class);
        $transportMock->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo(ReIdentificationClientV1::ENDPOINT_CREATE_RE_IDENTIFICATION),
                $this->equalTo($input),
                $this->equalTo($this->authorizationHeaders($input)),
                $this->equalTo([200, 201, 403, 404])
            )
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getBody' => $this->createConfiguredMock(
                    StreamInterface::class, [
                    'getContents' => json_encode(array_merge($input, [
                        'id' => 're-identification-id',
                    ])),
                ]),
                'getStatusCode' => 201,
            ]));

        $client = new ReIdentificationClientV1($this->hmac);
        $client->setTransport($transportMock);

        $response = $client->createReIdentification(
            new CreateReIdentificationRequest($input)
        );

        $this->assertEquals($response->toArray(), array_merge($input, [
            'id' => 're-identification-id',
        ]));
    }

    public function testCreateReIdentificationIfTransportThrowsException()
    {
        $this->expectException(InvalidStatusCodeException::class);
        $this->expectExceptionMessage('Invalid status code: 500');
        $input = $this->createReIdentificationInput();
        $transportMock = $this->createMock(HTTP::class);
        $transportMock->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo(ReIdentificationClientV1::ENDPOINT_CREATE_RE_IDENTIFICATION),
                $this->equalTo($input),
                $this->equalTo($this->authorizationHeaders($input)),
                $this->equalTo([200, 201, 403, 404])
            )
            ->willThrowException(new InvalidStatusCodeException(500));

        $client = new ReIdentificationClientV1($this->hmac);
        $client->setTransport($transportMock);

        $client->createReIdentification(
            new CreateReIdentificationRequest($input)
        );
    }

    public function testCreateReIdentificationIfDtoParserOfCreateReIdentificationResponseThrows()
    {
        $input = $this->createReIdentificationInput();
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("id is required");
        $transportMock = $this->createMock(HTTP::class);
        $transportMock->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo(ReIdentificationClientV1::ENDPOINT_CREATE_RE_IDENTIFICATION),
                $this->equalTo($input),
                $this->equalTo($this->authorizationHeaders($input)),
                $this->equalTo([200, 201, 403, 404])
            )
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getBody' => $this->createConfiguredMock(
                    StreamInterface::class, [
                    'getContents' => json_encode(array_merge($input, [
                        'not-id' => 're-identification-id',
                    ])),
                ]),
                'getStatusCode' => 201,
            ]));

        $client = new ReIdentificationClientV1($this->hmac);
        $client->setTransport($transportMock);

        $client->createReIdentification(
            new CreateReIdentificationRequest($input)
        );
    }

    public function testCreateReIdentificationThrowsFeatureNotEnabledException()
    {
        $this->expectException(FeatureNotEnabledException::class);
        $this->expectExceptionMessage('re-identification feature not enabled');
        $input = $this->createReIdentificationInput();
        $transportMock = $this->createMock(HTTP::class);
        $transportMock->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo(ReIdentificationClientV1::ENDPOINT_CREATE_RE_IDENTIFICATION),
                $this->equalTo($input),
                $this->equalTo($this->authorizationHeaders($input)),
                $this->equalTo([200, 201, 403, 404])
            )
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                    'getContents' => json_encode([
                        'message' => 're-identification feature not enabled',
                        'status_code' => 403,
                    ]),
                ]),
                'getStatusCode' => 403,
            ]));

        $client = new ReIdentificationClientV1($this->hmac);
        $client->setTransport($transportMock);

        $client->createReIdentification(new CreateReIdentificationRequest($input));
    }

    public function testCreateReIdentificationThrowsNoApprovedVerificationFoundException()
    {
        $this->expectException(NoApprovedVerificationFoundException::class);
        $this->expectExceptionMessage('no approved verification found for customer');
        $input = $this->createReIdentificationInput();
        $transportMock = $this->createMock(HTTP::class);
        $transportMock->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo(ReIdentificationClientV1::ENDPOINT_CREATE_RE_IDENTIFICATION),
                $this->equalTo($input),
                $this->equalTo($this->authorizationHeaders($input)),
                $this->equalTo([200, 201, 403, 404])
            )
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                    'getContents' => json_encode([
                        'message' => 'no approved verification found for customer',
                        'status_code' => 404,
                    ]),
                ]),
                'getStatusCode' => 404,
            ]));

        $client = new ReIdentificationClientV1($this->hmac);
        $client->setTransport($transportMock);

        $client->createReIdentification(new CreateReIdentificationRequest($input));
    }

    public function testGetReIdentification()
    {
        $output = $this->getReIdentificationOutput();
        $uri = sprintf(
            ReIdentificationClientV1::ENDPOINT_GET_RE_IDENTIFICATION,
            $output["id"]
        );

        $transportMock = $this->createMock(HTTP::class);
        $transportMock->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($uri),
                $this->equalTo($this->authorizationHeaders($uri)),
                $this->equalTo([200])
            )
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                    'getContents' => json_encode($output),
                ]),
                'getStatusCode' => 200,
            ]));

        $client = new ReIdentificationClientV1($this->hmac);
        $client->setTransport($transportMock);

        $response = $client->getReIdentification($output["id"]);

        $this->assertEquals($response->id, $output["id"]);
        $this->assertEquals($response->status, $output["status"]);
        $this->assertEquals($response->customer->id, $output["customer"]["id"]);
        $this->assertEquals($response->customer->email, $output["customer"]["email"]);
        $this->assertEquals($response->redirect_uri, $output["redirect_uri"]);
        $this->assertEquals($response->webhook, $output["webhook"]);
    }

    public function testGetReIdentificationIfTransportThrowsException()
    {
        $this->expectException(InvalidStatusCodeException::class);
        $this->expectExceptionMessage('Invalid status code: 500');
        $output = $this->getReIdentificationOutput();
        $uri = sprintf(
            ReIdentificationClientV1::ENDPOINT_GET_RE_IDENTIFICATION,
            $output["id"]
        );

        $transportMock = $this->createMock(HTTP::class);
        $transportMock->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($uri),
                $this->equalTo($this->authorizationHeaders($uri)),
                $this->equalTo([200])
            )
            ->willThrowException(new InvalidStatusCodeException(500));

        $client = new ReIdentificationClientV1($this->hmac);
        $client->setTransport($transportMock);

        $client->getReIdentification($output["id"]);
    }

    public function testGetReIdentificationIfDtoParserThrows()
    {
        $output = $this->getReIdentificationOutput();
        $id = $output["id"];
        unset($output["id"]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("id is required");
        $uri = sprintf(
            ReIdentificationClientV1::ENDPOINT_GET_RE_IDENTIFICATION,
            $id
        );

        $transportMock = $this->createMock(HTTP::class);
        $transportMock->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($uri),
                $this->equalTo($this->authorizationHeaders($uri)),
                $this->equalTo([200])
            )
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                    'getContents' => json_encode(array_merge($output, [
                        'not-id' => 're-identification-id',
                    ])),
                ]),
                'getStatusCode' => 200,
            ]));

        $client = new ReIdentificationClientV1($this->hmac);
        $client->setTransport($transportMock);

        $client->getReIdentification($id);
    }

    public function testSetBaseURL()
    {
        $transportMock = $this->createMock(HTTP::class);
        $transportMock->expects($this->once())
            ->method('setBaseURL')
            ->with($this->equalTo("https://example-base-url.com"));

        $client = new ReIdentificationClientV1($this->hmac);
        $client->setTransport($transportMock);
        $client->setBaseURL("https://example-base-url.com");
    }

    public function testUseSandbox()
    {
        $transportMock = $this->createMock(HTTP::class);
        $transportMock->expects($this->once())
            ->method('setBaseURL')
            ->with($this->equalTo(ReIdentificationClient::SANDBOX_URL));

        $client = new ReIdentificationClientV1($this->hmac);
        $client->setTransport($transportMock);
        $client->useSandbox();
    }

    private function createReIdentificationInput(): array
    {
        return [
            "customer" => [
                "id" => "customer-id",
                "email" => "customer-email@mock.com",
            ],
            "redirect_uri" => "https://redirect-uri.com",
            "webhook" => "https://webhook-uri.com",
        ];
    }

    private function getReIdentificationOutput(): array
    {
        return array_merge(
            $this->createReIdentificationInput(),
            [
                "id" => "re-identification-id",
                "status" => "pending",
            ]
        );
    }

    private function authorizationHeaders($input): array
    {
        return [
            "Authorization" => sprintf("hmac %s", $this->hmac->generate($input)),
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->hmac = new HMAC("api-key", "api-secret");
    }
}
