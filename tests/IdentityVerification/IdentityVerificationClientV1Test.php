<?php

namespace VerifyMyContent\SDK\IdentityVerification;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use VerifyMyContent\Commons\Security\HMAC;
use VerifyMyContent\Commons\Transport\HTTP;
use VerifyMyContent\Commons\Transport\InvalidStatusCodeException;
use VerifyMyContent\SDK\Core\Validator\ValidationException;
use VerifyMyContent\SDK\IdentityVerification\Entity\Requests\CreateIdentityVerificationRequest;

class IdentityVerificationClientV1Test extends TestCase
{
    /**
     * @var HMAC $hmac
     */
    private $hmac;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hmac = new HMAC("api-key", "api-secret");
    }

    private function createIdentityVerificationInput(): array
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

    private function authorizationHeaders($input): array
    {
        return [
            "Authorization" => sprintf("hmac %s", $this->hmac->generate($input)),
        ];
    }

    public function testCreateIdentityVerification()
    {
        $input = $this->createIdentityVerificationInput();
        $transportMock = $this->createMock(HTTP::class);
        $transportMock->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo(IdentityVerificationClientV1::ENDPOINT_CREATE_IDENTITY_CHECK),
                $this->equalTo($input),
                $this->equalTo($this->authorizationHeaders($input)),
                $this->equalTo([201])
            )
            ->willReturn($this->createConfiguredMock(\Psr\Http\Message\ResponseInterface::class, [
                'getBody' => $this->createConfiguredMock(
                    \Psr\Http\Message\StreamInterface::class, [
                    'getContents' => json_encode(array_merge($input, [
                        'id' => 'identity-verification-id',
                    ])),
                ]),
                'getStatusCode' => 201,
            ]));

        $client = new IdentityVerificationClientV1($this->hmac);
        $client->setTransport($transportMock);

        $response = $client->createIdentityVerification(
            new CreateIdentityVerificationRequest($input)
        );

        $this->assertEquals($response->toArray(), array_merge($input, [
            'id' => 'identity-verification-id',
        ]));
    }

    public function testCreateIdentityVerificationIfTransportThrowsException()
    {
        $this->expectException(InvalidStatusCodeException::class);
        $this->expectExceptionMessage('Invalid status code: 500');
        $input = $this->createIdentityVerificationInput();
        $transportMock = $this->createMock(HTTP::class);
        $transportMock->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo(IdentityVerificationClientV1::ENDPOINT_CREATE_IDENTITY_CHECK),
                $this->equalTo($input),
                $this->equalTo($this->authorizationHeaders($input)),
                $this->equalTo([201])
            )
            ->willThrowException(new InvalidStatusCodeException(500));

        $client = new IdentityVerificationClientV1($this->hmac);
        $client->setTransport($transportMock);

        $client->createIdentityVerification(
            new CreateIdentityVerificationRequest($input)
        );
    }

    public function testCreateIdentityVerificationIfDtoParserOfCreateIdentityVerificationResponseThrows(){

        $input = $this->createIdentityVerificationInput();
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("id is required");
        $transportMock = $this->createMock(HTTP::class);
        $transportMock->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo(IdentityVerificationClientV1::ENDPOINT_CREATE_IDENTITY_CHECK),
                $this->equalTo($input),
                $this->equalTo($this->authorizationHeaders($input)),
                $this->equalTo([201])
            )
            ->willReturn($this->createConfiguredMock(\Psr\Http\Message\ResponseInterface::class, [
                'getBody' => $this->createConfiguredMock(
                    \Psr\Http\Message\StreamInterface::class, [
                    'getContents' => json_encode(array_merge($input, [
                        'not-id' => 'identity-verification-id',
                    ])),
                ]),
                'getStatusCode' => 201,
            ]));

        $client = new IdentityVerificationClientV1($this->hmac);
        $client->setTransport($transportMock);

        $client->createIdentityVerification(
            new CreateIdentityVerificationRequest($input)
        );
    }

    public function testSetBaseURL(){
        $transportMock = $this->createMock(HTTP::class);
        $transportMock->expects($this->once())
            ->method('setBaseURL')
            ->with($this->equalTo("https://example-base-url.com"));

        $client = new IdentityVerificationClientV1($this->hmac);
        $client->setTransport($transportMock);
        $client->setBaseURL("https://example-base-url.com");
    }

    public function testUseSandbox(){
        $transportMock = $this->createMock(HTTP::class);
        $transportMock->expects($this->once())
            ->method('setBaseURL')
            ->with($this->equalTo(IdentityVerificationClient::SANDBOX_URL));

        $client = new IdentityVerificationClientV1($this->hmac);
        $client->setTransport($transportMock);
        $client->useSandbox();
    }

    private function getIdentityVerificationOutput(): array
    {
        return array_merge(
            $this->createIdentityVerificationInput(),
            ["id" => "identity-verification-id", "status" => IdentityVerificationStatus::PENDING]
        );
    }

    public function testGetIdentityVerification()
    {
        $output = $this->getIdentityVerificationOutput();
        $uri = sprintf(
            IdentityVerificationClientV1::ENDPOINT_GET_IDENTITY_CHECK,
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
            ->willReturn($this->createConfiguredMock(\Psr\Http\Message\ResponseInterface::class, [
                'getBody' => $this->createConfiguredMock(
                    \Psr\Http\Message\StreamInterface::class, [
                    'getContents' => json_encode($output),
                ]),
                'getStatusCode' => 200,
            ]));

        $client = new IdentityVerificationClientV1($this->hmac);
        $client->setTransport($transportMock);

        $response = $client->getIdentityVerification($output["id"]);

        $this->assertEquals($response->toArray(), $output);
    }

    public function testGetIdentityVerificationIfTransportThrowsException()
    {
        $this->expectException(InvalidStatusCodeException::class);
        $this->expectExceptionMessage('Invalid status code: 500');
        $output = $this->getIdentityVerificationOutput();
        $uri = sprintf(
            IdentityVerificationClientV1::ENDPOINT_GET_IDENTITY_CHECK,
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

        $client = new IdentityVerificationClientV1($this->hmac);
        $client->setTransport($transportMock);

        $client->getIdentityVerification($output["id"]);
    }

    public function testGetIdentityVerificationIfDtoParserOfGetIdentityVerificationResponseThrows(){

        $output = $this->getIdentityVerificationOutput();
        $id = $output["id"];
        unset($output["id"]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("id is required");
        $uri = sprintf(
            IdentityVerificationClientV1::ENDPOINT_GET_IDENTITY_CHECK,
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
            ->willReturn($this->createConfiguredMock(\Psr\Http\Message\ResponseInterface::class, [
                'getBody' => $this->createConfiguredMock(
                    \Psr\Http\Message\StreamInterface::class, [
                    'getContents' => json_encode(array_merge($output, [
                        'not-id' => 'identity-verification-id',
                    ])),
                ]),
                'getStatusCode' => 200,
            ]));

        $client = new IdentityVerificationClientV1($this->hmac);
        $client->setTransport($transportMock);

        $client->getIdentityVerification($id);
    }
}
