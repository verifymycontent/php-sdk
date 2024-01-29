# VerifyMyContent PHP SDK

PHP SDK to use the VerifyMyContent services (Identity Verification, Content Moderation, and Content Complaint).

## Installation

```bash
composer require verifymycontent/sdk
```

## Get Started

The main class to handle the moderation integration process is the `VerifyMyContent\VerifyMyContent`. It will abstract the HMAC generation for the API calls.


### Start an Identity Verification

Use the `createIdentityVerification` of the `VerifyMyContent\SDK\IdentityVerification\IdentityVerificationClient` abstraction inside `VerifyMyContent\VerifyMyContent` passing an `VerifyMyContent\SDK\IdentityVerification\Entity\Requests\CreateIdentityVerificationRequest` and receiving an `VerifyMyContent\SDK\IdentityVerification\Entity\Responses\CreateIdentityVerificationResponse`.

```php
<?php
require(__DIR__ . "/vendor/autoload.php");

$vmc = new VerifyMyContent\VerifyMyContent(getenv('VMC_API_KEY'), getenv('VMC_API_SECRET'));
//$vmc->useSandbox();

$response = $vmc->identityVerification->createIdentityVerification(
    new \VerifyMyContent\SDK\IdentityVerification\Entity\Requests\CreateIdentityVerificationRequest([
        "customer" => [
            "id" => "YOUR-CUSTOMER-UNIQUE-ID",
            "email" => "person@example.com",
            "phone" => "+4412345678"
        ],
        "redirect_uri" => "https://example.com/callback",
        "webhook" => "https://example.com/webhook",
    ])
);

// save $response->id if you want to save the verification of your customer

// redirect user to check identity
header("Location: {$response->redirect_uri}");
```

### Retrieve Identity Verification by ID

Retrieves a specific identity verification to get current status. 

- Pass the `id` of the identity verification to the `getIdentityVerification` method of the `VerifyMyContent\SDK\IdentityVerification\IdentityVerificationClient` abstraction inside `VerifyMyContent\VerifyMyContent`.
- Receive an `VerifyMyContent\SDK\IdentityVerification\Entity\Responses\GetIdentityVerificationResponse`.


```php
<?php
require(__DIR__ . "/vendor/autoload.php");

$vmc = new VerifyMyContent\VerifyMyContent(getenv('VMC_API_KEY'), getenv('VMC_API_SECRET'));
//$vmc->useSandbox();

$response = $vmc->identityVerification->getIdentityVerification("YOUR-IDENTITY-VERIFICATION-ID");

// Printing current status
echo "Status: {$response->status}";
```


### Receive an Identity Verification Webhook

Receive a webhook from VerifyMyContent when the identity verification status changes.

- Receive a webhook from VerifyMyContent with the `$_POST` data that can be parsed using the `VerifyMyContent\SDK\IdentityVerification\Entity\Requests\WebhookIdentityVerificationRequest` class.

```php
<?php
require(__DIR__ . "/vendor/autoload.php");

$data = json_decode(file_get_contents('php://input'), true);
$webhook = new \VerifyMyContent\SDK\IdentityVerification\Entity\Requests\WebhookIdentityVerificationRequest($data);

// Printing current status
echo "Status: {$webhook->status} received from verification {$webhook->id}";

// This is how you can check if the identity verification is approved.
if ($webhook->status === \VerifyMyContent\SDK\IdentityVerification\IdentityVerificationStatus::APPROVED) {
    // do your thing
}
```

### Create a Static Content Moderation

Use the `createStaticContentModeration` of the `VerifyMyContent\SDK\ContentModeration\ContentModerationClient` abstraction inside `VerifyMyContent\VerifyMyContent` passing an `VerifyMyContent\SDK\ContentModeration\Entity\Requests\CreateStaticContentModerationRequest` and receiving an `VerifyMyContent\SDK\ContentModeration\Entity\Responses\CreateStaticContentModerationResponse`.

```php
<?php

require(__DIR__ . "/vendor/autoload.php");

$vmc = new VerifyMyContent\VerifyMyContent(getenv('VMC_API_KEY'), getenv('VMC_API_SECRET'));
//$vmc->useSandbox();

$response = $vmc->contentModeration()->createStaticContentModeration([
  "content" => [
    "type" => "video",
    "external_id" => "YOUR-VIDEO-ID",
    "url" => "https://example.com/video.mp4",
    "title" => "Uploaded video title",
    "description" => "Uploaded video description",
  ],
  "webhook" => "https://example.com/webhook",
  "redirect_url" => "https://example.com/callback",
  "customer" => [
    "id" => "YOUR-CUSTOMER-UNIQUE-ID",
    "email" => "person@example.com",
    "phone" => "+4412345678"
  ],
  "type" => "face-match",
  "rule" => "default",
  "faces_id" => ["ID"],
  "collection_id" => "YOUR-COLLECTION-ID"
]);

// save $response->id if you want to call the moderation status endpoint later

// redirect uploader to check identity
header("Location: {$response->redirect_url}");
```

### Retrieve Static Content Moderation by ID

Retrieves a specific moderation to get current status. Example:
- Receive an `VerifyMyContent\SDK\ContentModeration\Entity\Responses\GetStaticContentModerationResponse`.

```php
<?php

require(__DIR__ . "/vendor/autoload.php");

$vmc = new VerifyMyContent\VerifyMyContent(getenv('VMC_API_KEY'), getenv('VMC_API_SECRET'));
//$vmc->useSandbox();

$response = $vmc->contentModeration()->getStaticContentModeration("YOUR-CONTENT-MODERATION-ID");

// Printing current status
echo "Status: {$response->status}";
```

### Receive a Static Content Moderation Webhook

- Receive a webhook from VerifyMyContent with the `$_POST` data that can be parsed using the `VerifyMyContent\SDK\ContentModeration\Entity\Requests\WebhookStaticContentModerationRequest` class.

```php
<?php
require(__DIR__ . "/vendor/autoload.php");

$data = json_decode(file_get_contents('php://input'), true);
$webhook = new \VerifyMyContent\SDK\ContentModeration\Entity\Requests\WebhookStaticContentModerationRequest($data);

// Printing current status
echo "Status: {$webhook->status} received from static content {$webhook->id}";

// This is how you can check if the moderation was approved.
if ($webhook->status === \VerifyMyContent\SDK\ContentModeration\ContentModerationStatus::STATIC_APPROVED) {
    // do your thing
}
```

## Live Content

To moderate a live stream broadcast you'll need to use different APIs as described below.

### Create a Live Content Moderation

Use the `createLiveContentModeration` of the `VerifyMyContent\SDK\ContentModeration\ContentModerationClient` abstraction inside `VerifyMyContent\VerifyMyContent` passing an `VerifyMyContent\SDK\ContentModeration\Entity\Requests\CreateLiveContentModerationRequest` and receiving an `VerifyMyContent\SDK\ContentModeration\Entity\Responses\CreateLiveContentModerationResponse`.

```php
<?php

require(__DIR__ . "/vendor/autoload.php");

$vmc = new VerifyMyContent\VerifyMyContent(getenv('VMC_API_KEY'), getenv('VMC_API_SECRET'));
//$vmc->useSandbox();

$response = $vmc->contentModeration()->createLiveContentModeration([
  "external_id" => "YOUR-LIVESTREAM-ID",
  "embed_url" => "https://example.com/live/",
  "title" => "Live stream title",
  "description" => "Live stream description",
  "webhook" => "https://example.com/webhook",
  "stream" => [
      "protocol" => "webrtc",
      "url" => "https://example.com/live/",
  ],
  "customer" => [
      "id" => "YOUR-CUSTOMER-UNIQUE-ID",
      "email" => "person@example.com",
      "phone" => "+4412345678"
  ],
  "type" => "face-match",
  "rule" => "default",
  "faces_id" => ["ID"],
  "collection_id" => "YOUR-COLLECTION-ID"
]);

// save $response->id to start live stream later

// redirect uploader to check identity
header("Location: {$response->login_url}");
```

### Start a created Live Content Moderation

When you receive the webhook with the status `Authorised`, it means you can now start to broadcast a live stream, you can then use the `startLiveContentModeration` method to trigger the moderation:

```php
<?php

require(__DIR__ . "/vendor/autoload.php");
    
$vmc = new VerifyMyContent\VerifyMyContent(getenv('VMC_API_KEY'), getenv('VMC_API_SECRET'));
//$vmc->useSandbox();

$vmc->contentModeration()->startLiveContentModeration("YOUR-CONTENT-MODERATION-ID");

// that's all folks!
```


### Receive a Live Content Moderation Webhook

- Receive a webhook from VerifyMyContent with the `$_POST` data that can be parsed using the `VerifyMyContent\SDK\ContentModeration\Entity\Requests\WebhookLiveContentModerationRequest` class.

```php
<?php
require(__DIR__ . "/vendor/autoload.php");

$data = json_decode(file_get_contents('php://input'), true);
$webhook = new \VerifyMyContent\SDK\ContentModeration\Entity\Requests\WebhookLiveContentModerationRequest($data);

// Printing current status
echo "Status: {$webhook->status} received from live content {$webhook->id}";

// This is how you can check if the live stream is authorized.
if ($webhook->status === \VerifyMyContent\SDK\ContentModeration\ContentModerationStatus::LIVE_AUTHORIZED
    // do your thing
}
```


### Updating Live Stream moderation rules

This endpoint allows you to update the moderation rules for a specific live stream

```php
<?php

require(__DIR__ . "/vendor/autoload.php");
    
$vmc = new VerifyMyContent\VerifyMyContent(getenv('VMC_API_KEY'), getenv('VMC_API_SECRET'));
//$vmc->useSandbox();

$vmc->contentModeration()->changeLiveContentRule("YOUR-CONTENT-MODERATION-ID");
```


### Pausing Live Stream moderation

This endpoint allows you to pause the moderation for a specific live stream

```php
<?php

require(__DIR__ . "/vendor/autoload.php");
    
$vmc = new VerifyMyContent\VerifyMyContent(getenv('VMC_API_KEY'), getenv('VMC_API_SECRET'));
//$vmc->useSandbox();

$vmc->contentModeration()->pauseLivestream("YOUR-CONTENT-MODERATION-ID");
```


### Resume Live Stream moderation

This endpoint allows you to resume the moderation for a specific live stream

```php
<?php

require(__DIR__ . "/vendor/autoload.php");
    
$vmc = new VerifyMyContent\VerifyMyContent(getenv('VMC_API_KEY'), getenv('VMC_API_SECRET'));
//$vmc->useSandbox();

$vmc->contentModeration()->resumeLivestream("YOUR-CONTENT-MODERATION-ID");
```
