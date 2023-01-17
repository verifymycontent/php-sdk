<?php

namespace ContentModeration;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use VerifyMyContent\Commons\Security\HMAC;
use VerifyMyContent\Commons\Transport\HTTP;
use VerifyMyContent\Commons\Transport\InvalidStatusCodeException;
use VerifyMyContent\SDK\ContentModeration\ContentModerationClient;
use VerifyMyContent\SDK\ContentModeration\ContentModerationClientV1;
use VerifyMyContent\SDK\ContentModeration\Entity\Requests\CreateAnonymousLiveContentModerationRequest;
use VerifyMyContent\SDK\ContentModeration\Entity\Requests\CreateLiveContentModerationRequest;
use VerifyMyContent\SDK\ContentModeration\Entity\Requests\CreateStaticContentModerationRequest;
use VerifyMyContent\SDK\Core\Validator\ValidationException;

class ContentModerationClientV1Test extends TestCase
{
    /**
     * @var HMAC $hmac
     */
    private $hmac;

    public function testUseSandbox()
    {
        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $mockTransport->expects($this->once())
            ->method('setBaseURL')
            ->with(ContentModerationClient::SANDBOX_URL);

        $client->setTransport($mockTransport);

        $client->useSandbox();
    }

    private function newCmc(): ContentModerationClientV1
    {
        return new ContentModerationClientV1($this->hmac);
    }

    private function mockTransport()
    {
        return $this->createMock(HTTP::class);
    }

    public function testSetBaseURL()
    {
        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $mockTransport->expects($this->once())
            ->method('setBaseURL')
            ->with('https://example.com');

        $client->setTransport($mockTransport);

        $client->setBaseURL('https://example.com');
    }

    public function testCreateStaticContentModeration()
    {
        $input = $this->staticContentInput();
        $output = $this->staticContentOutput();
        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $mockTransport->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo(ContentModerationClientV1::ENDPOINT_CREATE_STATIC_CONTENT_MODERATION),
                $this->equalTo($input),
                $this->equalTo(['Authorization' => $this->hmac->generate($input, true)]),
                [201]
            )
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getStatusCode' => 201,
                'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                    'getContents' => json_encode($output)
                ])
            ]));

        $client->setTransport($mockTransport);
        $response = $client->createStaticContentModeration(new CreateStaticContentModerationRequest($input));

        $this->assertEquals($output['id'], $response->id);
        $this->assertEquals($output['redirect_url'], $response->redirect_url);
        $this->assertEquals($output['external_id'], $response->external_id);
        $this->assertEquals($output['status'], $response->status);
        $this->assertEquals($output['notes'], $response->notes);
        $this->assertEquals($output['tags'], $response->tags);
        $this->assertEquals($output['created_at'], $response->created_at->format('Y-m-d H:i:s'));
        $this->assertEquals($output['updated_at'], $response->updated_at->format('Y-m-d H:i:s'));
    }

  public function testCreateStaticContentModerationV2()
  {
    $input = $this->staticContentInput();
    $output = $this->staticContentOutput();
    $client = $this->newCmc();
    $mockTransport = $this->mockTransport();
    $mockTransport->expects($this->once())
      ->method('post')
      ->with(
        $this->equalTo(ContentModerationClientV1::ENDPOINT_CREATE_STATIC_CONTENT_MODERATION_V2),
        $this->equalTo($input),
        $this->equalTo(['Authorization' => $this->hmac->generate($input, true)]),
        [201]
      )
      ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
        'getStatusCode' => 201,
        'getBody' => $this->createConfiguredMock(StreamInterface::class, [
          'getContents' => json_encode($output)
        ])
      ]));

    $client->setTransport($mockTransport);
    $response = $client->createStaticContentModerationV2(new CreateStaticContentModerationRequest($input));

    $this->assertEquals($output['id'], $response->id);
    $this->assertEquals($output['redirect_url'], $response->redirect_url);
    $this->assertEquals($output['external_id'], $response->external_id);
    $this->assertEquals($output['status'], $response->status);
    $this->assertEquals($output['notes'], $response->notes);
    $this->assertEquals($output['tags'], $response->tags);
    $this->assertEquals($output['created_at'], $response->created_at->format('Y-m-d H:i:s'));
    $this->assertEquals($output['updated_at'], $response->updated_at->format('Y-m-d H:i:s'));
  }

    private function staticContentInput(): array
    {
        return [
            'content' => [
                'external_id' => 'YOUR-VIDEO-ID',
                'type' => 'video',
                'url' => 'https://example.com/video.mp4',
                'title' => 'Your title',
                'description' => 'Your description'
            ],
            'customer' => [
                'id' => 'YOUR-USER-ID',
                'email' => 'person@example.com',
                'phone' => '+4412345678',
            ],
            'webhook' => 'https://example.com/webhook',

        ];
    }

    private function staticContentOutput(): array
    {
        return [
            "id" => "ABC-123-5678-ABC",
            "redirect_url" => "https://app.verifymycontent.com/v/ABC-123-5678-ABC",
            "external_id" => "YOUR-CORRELATION-ID",
            "status" => "Rejected",
            "notes" => "Harmful content found.",
            "tags" => [
                "UNDERAGE"
            ],
            "created_at" => "2020-11-12 19:06:00",
            "updated_at" => "2020-11-12 19:06:00"
        ];
    }

    public function testCreateStaticContentModerationWithInvalidStatusCode()
    {
        $input = $this->staticContentInput();
        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $mockTransport->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo(ContentModerationClientV1::ENDPOINT_CREATE_STATIC_CONTENT_MODERATION),
                $this->equalTo($input),
                $this->equalTo(['Authorization' => $this->hmac->generate($input, true)]),
                [201]
            )
            ->willThrowException(new InvalidStatusCodeException(400));

        $client->setTransport($mockTransport);
        $this->expectException(InvalidStatusCodeException::class);
        $client->createStaticContentModeration(new CreateStaticContentModerationRequest($input));
    }

    public function testCreateStaticContentModerationWithInvalidResponse()
    {
        $input = $this->staticContentInput();
        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $mockTransport->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo(ContentModerationClientV1::ENDPOINT_CREATE_STATIC_CONTENT_MODERATION),
                $this->equalTo($input),
                $this->equalTo(['Authorization' => $this->hmac->generate($input, true)]),
                [201]
            )
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getStatusCode' => 201,
                'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                    'getContents' => json_encode([])
                ])
            ]));

        $client->setTransport($mockTransport);
        $this->expectException(ValidationException::class);
        $client->createStaticContentModeration(new CreateStaticContentModerationRequest($input));
    }

    public function testGetStaticContentModeration()
    {
        $output = $this->staticContentOutput();
        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = sprintf(ContentModerationClientV1::ENDPOINT_GET_STATIC_CONTENT_MODERATION, $output['id']);

        $mockTransport->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($uri),
                $this->equalTo(['Authorization' => $this->hmac->generate($uri, true)]),
                [200]
            )
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getStatusCode' => 200,
                'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                    'getContents' => json_encode($output)
                ])
            ]));

        $client->setTransport($mockTransport);
        $response = $client->getStaticContentModeration($output['id']);

        $this->assertEquals($output['id'], $response->id);
        $this->assertEquals($output['redirect_url'], $response->redirect_url);
        $this->assertEquals($output['external_id'], $response->external_id);
        $this->assertEquals($output['status'], $response->status);
        $this->assertEquals($output['notes'], $response->notes);
        $this->assertEquals($output['tags'], $response->tags);
        $this->assertEquals($output['created_at'], $response->created_at->format('Y-m-d H:i:s'));
        $this->assertEquals($output['updated_at'], $response->updated_at->format('Y-m-d H:i:s'));
    }

    public function testGetStaticContentModerationWithInvalidStatusCode()
    {
        $output = $this->staticContentOutput();
        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = sprintf(ContentModerationClientV1::ENDPOINT_GET_STATIC_CONTENT_MODERATION, $output['id']);

        $mockTransport->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($uri),
                $this->equalTo(['Authorization' => $this->hmac->generate($uri, true)]),
                [200]
            )
            ->willThrowException(new InvalidStatusCodeException(400));

        $client->setTransport($mockTransport);
        $this->expectException(InvalidStatusCodeException::class);
        $client->getStaticContentModeration($output['id']);
    }

    public function testGetStaticContentModerationWithInvalidResponse()
    {
        $output = $this->staticContentOutput();
        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = sprintf(ContentModerationClientV1::ENDPOINT_GET_STATIC_CONTENT_MODERATION, $output['id']);

        $mockTransport->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($uri),
                $this->equalTo(['Authorization' => $this->hmac->generate($uri, true)]),
                [200]
            )
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getStatusCode' => 200,
                'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                    'getContents' => json_encode([])
                ])
            ]));

        $client->setTransport($mockTransport);
        $this->expectException(ValidationException::class);
        $client->getStaticContentModeration($output['id']);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->hmac = new HMAC('api-key', 'api-secret');
    }

    private function staticContentParticipantsOutput(): array
    {
        return [
            "id" => "ABC-123-5678-ABC",
            "customer" => [
                "id" => "CUSTOMER-ID",
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
            ],
            "face" => "https://example.com/face.jpg",
        ];
    }

    public function testGetStaticContentModerationParticipantsResponse()
    {
        $output = $this->staticContentParticipantsOutput();

        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = sprintf(ContentModerationClientV1::ENDPOINT_GET_STATIC_CONTENT_MODERATION_PARTICIPANTS, $output['id']);

        $mockTransport->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($uri),
                $this->equalTo(['Authorization' => $this->hmac->generate($uri, true)]),
                [200]
            )
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getStatusCode' => 200,
                'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                    'getContents' => json_encode($output)
                ])
            ]));

        $client->setTransport($mockTransport);
        $response = $client->getStaticContentModerationParticipants($output['id']);

        $this->assertEquals($output['id'], $response->id);
        $this->assertEquals($output['customer']['id'], $response->customer->id);
        $this->assertEquals($output['customer']['document']['type'], $response->customer->document->type);
        $this->assertEquals($output['customer']['document']['country'], $response->customer->document->country);
        $this->assertEquals($output['customer']['document']['number'], $response->customer->document->number);
        $this->assertEquals($output['customer']['document']['issued_at'], $response->customer->document->issued_at);
        $this->assertEquals($output['customer']['document']['expires_at'], $response->customer->document->expires_at);
        $this->assertEquals($output['customer']['document']['name'], $response->customer->document->name);
        $this->assertEquals($output['customer']['document']['dob'], $response->customer->document->dob->format('Y-m-d'));
        $this->assertEquals($output['customer']['document']['mrz'][0], $response->customer->document->mrz[0]);
        $this->assertEquals($output['customer']['document']['mrz'][1], $response->customer->document->mrz[1]);
        $this->assertEquals($output['customer']['document']['photos'][0], $response->customer->document->photos[0]);
        $this->assertEquals($output['customer']['document']['photos'][1], $response->customer->document->photos[1]);
        $this->assertEquals($output['face'], $response->face);
    }

    public function testGetStaticContentModerationParticipantsWithInvalidStatusCode()
    {
        $output = $this->staticContentParticipantsOutput();
        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = sprintf(ContentModerationClientV1::ENDPOINT_GET_STATIC_CONTENT_MODERATION_PARTICIPANTS, $output['id']);

        $mockTransport->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($uri),
                $this->equalTo(['Authorization' => $this->hmac->generate($uri, true)]),
                [200]
            )
            ->willThrowException(new InvalidStatusCodeException(400));

        $client->setTransport($mockTransport);
        $this->expectException(InvalidStatusCodeException::class);
        $client->getStaticContentModerationParticipants($output['id']);
    }

    public function testGetStaticContentModerationParticipantsWithInvalidJson()
    {
        $output = $this->staticContentParticipantsOutput();
        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = sprintf(ContentModerationClientV1::ENDPOINT_GET_STATIC_CONTENT_MODERATION_PARTICIPANTS, $output['id']);

        $mockTransport->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($uri),
                $this->equalTo(['Authorization' => $this->hmac->generate($uri, true)]),
                [200]
            )
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getStatusCode' => 200,
                'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                    'getContents' => json_encode([])
                ])
            ]));

        $client->setTransport($mockTransport);
        $this->expectException(ValidationException::class);
        $client->getStaticContentModerationParticipants($output['id']);
    }

    public function testGetStaticContentModerationParticipantsWithInvalidJson2()
    {
        $output = $this->staticContentParticipantsOutput();
        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = sprintf(ContentModerationClientV1::ENDPOINT_GET_STATIC_CONTENT_MODERATION_PARTICIPANTS, $output['id']);

        $mockTransport->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($uri),
                $this->equalTo(['Authorization' => $this->hmac->generate($uri, true)]),
                [200]
            )
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getStatusCode' => 200,
                'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                    'getContents' => json_encode(['id' => '123'])
                ])
            ]));

        $client->setTransport($mockTransport);
        $this->expectException(ValidationException::class);
        $client->getStaticContentModerationParticipants($output['id']);
    }

    public function testStartLiveContentModeration()
    {
        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = sprintf(ContentModerationClientV1::ENDPOINT_START_LIVE_CONTENT_MODERATION, '123');

        $mockTransport->expects($this->once())
            ->method('patch')
            ->with(
                $this->equalTo($uri),
                $this->equalTo(null),
                $this->equalTo(['Authorization' => $this->hmac->generate($uri, true)]),
                [204]
            );

        $client->setTransport($mockTransport);
        $client->startLiveContentModeration('123');

        $this->assertTrue(true);
    }

    public function testStartLiveContentModerationWithInvalidStatusCode()
    {
        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = sprintf(ContentModerationClientV1::ENDPOINT_START_LIVE_CONTENT_MODERATION, '123');

        $mockTransport->expects($this->once())
            ->method('patch')
            ->with(
                $this->equalTo($uri),
                $this->equalTo(null),
                $this->equalTo(['Authorization' => $this->hmac->generate($uri, true)]),
                [204]
            )
            ->willThrowException(new InvalidStatusCodeException(400));

        $client->setTransport($mockTransport);
        $this->expectException(InvalidStatusCodeException::class);
        $client->startLiveContentModeration('123');
    }

    private function liveContentModerationInput()
    {
        return [
            "external_id" => "YOUR-LIVESTREAM-ID",
            "embed_url" => "https://example.com/live-stream-embed",
            "title" => "Your title",
            "description" => "Your description",
            "stream" => [
                "protocol" => "rtmps",
                "url" => "rtmps://your-server:443/your-video-stream"
            ],
            "webhook" => "https://example.com/webhook",
            "customer" => [
                "id" => "YOUR-USER-ID",
                "email" => "person@example.com",
                "phone" => "+4412345678"
            ]
        ];
    }

    private function liveContentModerationOutput()
    {
        return [
            "id" => "ABC-123-5678-ABC",
            "login_url" => "https://app.verifymycontent.com/v/ABC-123-5678-ABC",
            "external_id" => "YOUR-CORRELATION-ID",
            "status" => "Started",
            "notes" => "Harmful content found.",
            "tags" => [
                "UNDERAGE"
            ],
            "created_at" => "2020-11-12 19:06:00",
            "updated_at" => "2020-11-12 19:06:00"
        ];
    }

    public function testCreateLiveContentModeration()
    {
        $input = $this->liveContentModerationInput();
        $output = $this->liveContentModerationOutput();

        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = ContentModerationClientV1::ENDPOINT_CREATE_LIVE_CONTENT_MODERATION;
        $mockTransport->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo($uri),
                $this->equalTo($input),
                $this->equalTo(['Authorization' => $this->hmac->generate($input, true)]),
                [201]
            )
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getStatusCode' => 201,
                'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                    'getContents' => json_encode($output)
                ])
            ]));

        $client->setTransport($mockTransport);
        $response = $client->createLiveContentModeration(new CreateLiveContentModerationRequest($input));
        $this->assertEquals($output['id'], $response->id);
        $this->assertEquals($output['login_url'], $response->login_url);
        $this->assertEquals($output['external_id'], $response->external_id);
        $this->assertEquals($output['status'], $response->status);
        $this->assertEquals($output['notes'], $response->notes);
        $this->assertEquals($output['tags'], $response->tags);
        $this->assertEquals($output['created_at'], $response->created_at->format('Y-m-d H:i:s'));
        $this->assertEquals($output['updated_at'], $response->updated_at->format('Y-m-d H:i:s'));
    }

    public function testCreateLiveContentModerationWithInvalidStatusCode()
    {
        $input = $this->liveContentModerationInput();
        $output = $this->liveContentModerationOutput();

        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = ContentModerationClientV1::ENDPOINT_CREATE_LIVE_CONTENT_MODERATION;
        $mockTransport->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo($uri),
                $this->equalTo($input),
                $this->equalTo(['Authorization' => $this->hmac->generate($input, true)]),
                [201]
            )
            ->willThrowException(new InvalidStatusCodeException(400));

        $client->setTransport($mockTransport);
        $this->expectException(InvalidStatusCodeException::class);
        $client->createLiveContentModeration(new CreateLiveContentModerationRequest($input));
    }

    public function testCreateLiveContentModerationWithInvalidJson()
    {
        $input = $this->liveContentModerationInput();

        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = ContentModerationClientV1::ENDPOINT_CREATE_LIVE_CONTENT_MODERATION;
        $mockTransport->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo($uri),
                $this->equalTo($input),
                $this->equalTo(['Authorization' => $this->hmac->generate($input, true)]),
                [201]
            )
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getStatusCode' => 201,
                'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                    'getContents' => json_encode([]),
                ])
            ]));

        $client->setTransport($mockTransport);
        $this->expectException(ValidationException::class);
        $client->createLiveContentModeration(new CreateLiveContentModerationRequest($input));
    }

    public function testGetLiveContentModeration()
    {
        $output = $this->liveContentModerationOutput();

        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = sprintf(ContentModerationClientV1::ENDPOINT_GET_LIVE_CONTENT_MODERATION, $output['id']);
        $mockTransport->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($uri),
                $this->equalTo(['Authorization' => $this->hmac->generate($uri, true)]),
                [200]
            )
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getStatusCode' => 200,
                'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                    'getContents' => json_encode($output)
                ])
            ]));

        $client->setTransport($mockTransport);
        $response = $client->getLiveContentModeration($output['id']);
        $this->assertEquals($output['id'], $response->id);
        $this->assertEquals($output['login_url'], $response->login_url);
        $this->assertEquals($output['external_id'], $response->external_id);
        $this->assertEquals($output['status'], $response->status);
        $this->assertEquals($output['notes'], $response->notes);
        $this->assertEquals($output['tags'], $response->tags);
        $this->assertEquals($output['created_at'], $response->created_at->format('Y-m-d H:i:s'));
        $this->assertEquals($output['updated_at'], $response->updated_at->format('Y-m-d H:i:s'));
    }

    public function testGetLiveContentModerationWithInvalidStatusCode()
    {
        $output = $this->liveContentModerationOutput();

        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = sprintf(ContentModerationClientV1::ENDPOINT_GET_LIVE_CONTENT_MODERATION, $output['id']);
        $mockTransport->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($uri),
                $this->equalTo(['Authorization' => $this->hmac->generate($uri, true)]),
                [200]
            )
            ->willThrowException(new InvalidStatusCodeException(400));

        $client->setTransport($mockTransport);
        $this->expectException(InvalidStatusCodeException::class);
        $client->getLiveContentModeration($output['id']);
    }

    public function testGetLiveContentModerationWithInvalidJson()
    {
        $output = $this->liveContentModerationOutput();

        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = sprintf(ContentModerationClientV1::ENDPOINT_GET_LIVE_CONTENT_MODERATION, $output['id']);
        $mockTransport->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($uri),
                $this->equalTo(['Authorization' => $this->hmac->generate($uri, true)]),
                [200]
            )
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getStatusCode' => 200,
                'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                    'getContents' => json_encode([]),
                ])
            ]));

        $client->setTransport($mockTransport);
        $this->expectException(ValidationException::class);
        $client->getLiveContentModeration($output['id']);
    }

    public function testCreateAnonymousLiveContentModeration()
    {
        $output = $this->liveContentModerationOutput();
        $input = $this->liveContentModerationInput();

        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = ContentModerationClientV1::ENDPOINT_CREATE_ANONYMOUS_LIVE_CONTENT_MODERATION;
        $mockTransport->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo($uri),
                $this->equalTo($input),
                $this->equalTo(['Authorization' => $this->hmac->generate($input, true)]),
                [201]
            )
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getStatusCode' => 201,
                'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                    'getContents' => json_encode($output)
                ])
            ]));

        $client->setTransport($mockTransport);
        $response = $client->createAnonymousLiveContentModeration(new CreateAnonymousLiveContentModerationRequest($input));
        $this->assertEquals($output['id'], $response->id);
        $this->assertEquals($output['login_url'], $response->login_url);
        $this->assertEquals($output['external_id'], $response->external_id);
        $this->assertEquals($output['status'], $response->status);
        $this->assertEquals($output['notes'], $response->notes);
        $this->assertEquals($output['tags'], $response->tags);
        $this->assertEquals($output['created_at'], $response->created_at->format('Y-m-d H:i:s'));
        $this->assertEquals($output['updated_at'], $response->updated_at->format('Y-m-d H:i:s'));
    }

    public function testCreateAnonymousLiveContentModerationWithInvalidStatusCode()
    {
        $input = $this->liveContentModerationInput();

        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = ContentModerationClientV1::ENDPOINT_CREATE_ANONYMOUS_LIVE_CONTENT_MODERATION;
        $mockTransport->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo($uri),
                $this->equalTo($input),
                $this->equalTo(['Authorization' => $this->hmac->generate($input, true)]),
                [201]
            )
            ->willThrowException(new InvalidStatusCodeException(400));

        $client->setTransport($mockTransport);
        $this->expectException(InvalidStatusCodeException::class);
        $client->createAnonymousLiveContentModeration(new CreateAnonymousLiveContentModerationRequest($input));
    }

    public function testCreateAnonymousLiveContentModerationWithInvalidJson()
    {
        $input = $this->liveContentModerationInput();

        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = ContentModerationClientV1::ENDPOINT_CREATE_ANONYMOUS_LIVE_CONTENT_MODERATION;
        $mockTransport->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo($uri),
                $this->equalTo($input),
                $this->equalTo(['Authorization' => $this->hmac->generate($input, true)]),
                [201]
            )
            ->willReturn($this->createConfiguredMock(ResponseInterface::class, [
                'getStatusCode' => 201,
                'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                    'getContents' => json_encode([])
                ])
            ]));

        $client->setTransport($mockTransport);
        $this->expectException(ValidationException::class);
        $client->createAnonymousLiveContentModeration(new CreateAnonymousLiveContentModerationRequest($input));
    }
}
