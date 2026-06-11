<?php

use VerifyMyContent\SDK\ReIdentification\Entity\Requests\CreateReIdentificationRequest;
use VerifyMyContent\SDK\ReIdentification\ReIdentificationStatus;
use VerifyMyContent\SDK\VerifyMyContent;

require_once __DIR__ . "/../../vendor/autoload.php";

# Getting env variables
$API_KEY = getenv("API_KEY");
$API_SECRET = getenv("API_SECRET");

# Setup SDK
$vmc = new VerifyMyContent($API_KEY, $API_SECRET);
$vmc->useSandbox();

// Create Re-Identification
$request = new CreateReIdentificationRequest([
    "customer" => [
        "id" => "example-php",
        "email" => "example-php@verifymycontent.com",
    ],
    "redirect_uri" => "https://example.com/callback",
    "webhook" => "https://example.com/webhook",
]);
$response = $vmc->reIdentification()->createReIdentification($request);

echo "To re-identify, please access: {$response->redirect_uri}\n";

// Wait until verification is approved
while (true) {
    echo "Checking status...";
    $response = $vmc->reIdentification()->getReIdentification($response->id);
    if ($response->status == ReIdentificationStatus::APPROVED) {
        echo "\n\nVerification {$response->id} for user {$response->customer->id} is approved!\n";
        break;
    }

    echo " " . $response->status . "\n";
    sleep(5);
}
