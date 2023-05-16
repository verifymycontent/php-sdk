<?php

namespace VerifyMyContent\SDK\IdentityVerification;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
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

    private function arrayExcept(array $array, array $keys): array
    {
        return array_diff_key($array, array_flip($keys));
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
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getBody' => $this->createConfiguredMock(
                    StreamInterface::class, [
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

    public function testCreateIdentityVerificationIfDtoParserOfCreateIdentityVerificationResponseThrows()
    {

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
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getBody' => $this->createConfiguredMock(
                    StreamInterface::class, [
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

    public function testSetBaseURL()
    {
        $transportMock = $this->createMock(HTTP::class);
        $transportMock->expects($this->once())
            ->method('setBaseURL')
            ->with($this->equalTo("https://example-base-url.com"));

        $client = new IdentityVerificationClientV1($this->hmac);
        $client->setTransport($transportMock);
        $client->setBaseURL("https://example-base-url.com");
    }

    public function testUseSandbox()
    {
        $transportMock = $this->createMock(HTTP::class);
        $transportMock->expects($this->once())
            ->method('setBaseURL')
            ->with($this->equalTo(IdentityVerificationClient::SANDBOX_URL));

        $client = new IdentityVerificationClientV1($this->hmac);
        $client->setTransport($transportMock);
        $client->useSandbox();
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
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getBody' => $this->createConfiguredMock(
                    StreamInterface::class, [
                    'getContents' => json_encode($output),
                ]),
                'getStatusCode' => 200,
            ]));

        $client = new IdentityVerificationClientV1($this->hmac);
        $client->setTransport($transportMock);

        $response = $client->getIdentityVerification($output["id"]);

        $this->assertEquals($response->id, $output["id"]);
        $this->assertEquals($response->status, $output["status"]);
        $this->assertEquals($response->customer->id, $output["customer"]["id"]);
        $this->assertEquals($response->customer->email, $output["customer"]["email"]);
        $this->assertEquals($response->redirect_uri, $output["redirect_uri"]);
        $this->assertEquals($response->webhook, $output["webhook"]);
    }

    public function testGetIdentityVerificationWithNoDocument()
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
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getBody' => $this->createConfiguredMock(
                    StreamInterface::class, [
                    'getContents' => json_encode($this->arrayExcept($output, ["document", "face"])),
                ]),
                'getStatusCode' => 200,
            ]));

        $client = new IdentityVerificationClientV1($this->hmac);
        $client->setTransport($transportMock);

        $response = $client->getIdentityVerification($output["id"]);

        $this->assertEquals($response->id, $output["id"]);
        $this->assertEquals($response->status, $output["status"]);
        $this->assertEquals($response->customer->id, $output["customer"]["id"]);
        $this->assertEquals($response->customer->email, $output["customer"]["email"]);
        $this->assertEquals($response->redirect_uri, $output["redirect_uri"]);
        $this->assertEquals($response->webhook, $output["webhook"]);
    }

    private function getIdentityVerificationOutput(): array
    {
        return array_merge(
            $this->createIdentityVerificationInput(),
            [
                "id" => "identity-verification-id",
                "status" => IdentityVerificationStatus::PENDING,
                "document" => [
                    "type" => "driving-license",
                    "country" => "GBR",
                    "number" => "ABC-123",
                    "issued_at" => "2017-04-13",
                    "expires_at" => "2027-04-13",
                    "name" => "John Snow",
                    "dob" => "1991-09-06",
                    "mrz" => [
                        "P<GBRSNOW<<JOHN<<<<<<<<<<<<<<<<<<<<",
                        "123456789012345<<<<<<<<<<<<<<<<<<00"
                    ],
                    "photos" => [
                        "https://docs.verifymycontent.com/front.jpg",
                        "https://docs.verifymycontent.com/back.jpg"
                    ]
                ],
                "face" => "https://docs.verifymycontent.com/face.jpg",
            ]
        );
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

    public function testGetIdentityVerificationIfDtoParserOfGetIdentityVerificationResponseThrows()
    {

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
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getBody' => $this->createConfiguredMock(
                    StreamInterface::class, [
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->hmac = new HMAC("api-key", "api-secret");
    }
}
