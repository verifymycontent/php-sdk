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
use VerifyMyContent\SDK\ContentModeration\Entity\Requests\ChangeLiveContentRuleRequest;
use VerifyMyContent\SDK\ContentModeration\Entity\Requests\StartLiveContentModerationRequest;
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

    public function testStartLiveContentModeration()
    {
        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = sprintf(ContentModerationClientV1::ENDPOINT_START_LIVE_CONTENT_MODERATION, '123');
        $input = $this->startLiveContentModerationInput();

        $mockTransport->expects($this->once())
            ->method('patch')
            ->with(
                $this->equalTo($uri),
                $this->equalTo($input),
                $this->equalTo(['Authorization' => $this->hmac->generate($uri, true)]),
                [204]
            );

        $client->setTransport($mockTransport);
        $client->startLiveContentModeration('123', new StartLiveContentModerationRequest($input));

        $this->assertTrue(true);
    }


    private function startLiveContentModerationInput()
    {
        return [
            "embed_url" => "https://example.com/live-stream-embed",
            "stream" => [
                "protocol" => "rtmps",
                "url" => "rtmps://your-server:443/your-video-stream"
            ],
        ];
    }

    public function testStartLiveContentModerationWithInvalidStatusCode()
    {
        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = sprintf(ContentModerationClientV1::ENDPOINT_START_LIVE_CONTENT_MODERATION, '123');
        $input = $this->startLiveContentModerationInput();

        $mockTransport->expects($this->once())
            ->method('patch')
            ->with(
                $this->equalTo($uri),
                $this->equalTo($input),
                $this->equalTo(['Authorization' => $this->hmac->generate($uri, true)]),
                [204]
            )
            ->willThrowException(new InvalidStatusCodeException(400));

        $client->setTransport($mockTransport);
        $this->expectException(InvalidStatusCodeException::class);
        $client->startLiveContentModeration('123', new StartLiveContentModerationRequest($input));
    }
    
    private function liveContentModerationInput($rule = 'default')
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
            "rule" => $rule,
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

    private function changeLiveContentRule($rule = 'default')
    {
        return [
           'rule' => $rule,
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

    public function testCreateLiveContentModerationWithNoNudityRule()
    {
        $input = $this->liveContentModerationInput("no-nudity");
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


    public function testChangeLiveContentRule()
    {
        $input = $this->changeLiveContentRule();
        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = sprintf(ContentModerationClientV1::ENDPOINT_CHANGE_LIVE_CONTENT_RULE, '123');

        $mockTransport->expects($this->once())
            ->method('patch')
            ->with(
                $this->equalTo($uri),
                $this->equalTo($input),
                $this->equalTo(['Authorization' => $this->hmac->generate($input, true)]),
                [204]
            );

        $client->setTransport($mockTransport);
        $client->changeLiveContentRule('123', new ChangeLiveContentRuleRequest($input));

        $this->assertTrue(true);
    }

    public function testChangeLiveContentRuleInvalidStatusCode()
    {
        $input = $this->changeLiveContentRule();
        $client = $this->newCmc();
        $mockTransport = $this->mockTransport();
        $uri = sprintf(ContentModerationClientV1::ENDPOINT_CHANGE_LIVE_CONTENT_RULE, '123');

        $mockTransport->expects($this->once())
            ->method('patch')
            ->with(
                $this->equalTo($uri),
                $this->equalTo($input),
                $this->equalTo(['Authorization' => $this->hmac->generate($input, true)]),
                [204]
            )
            ->willThrowException(new InvalidStatusCodeException(400));

        $client->setTransport($mockTransport);
        $this->expectException(InvalidStatusCodeException::class);
        $client->changeLiveContentRule('123', new ChangeLiveContentRuleRequest($input));
    }
}
