<?php

use VerifyMyContent\SDK\IdentityVerification\Entity\Requests\CreateIdentityVerificationRequest;
use VerifyMyContent\SDK\IdentityVerification\IdentityVerificationStatus;
use VerifyMyContent\SDK\VerifyMyContent;

require_once __DIR__ . "/../../vendor/autoload.php";

# Getting env variables
$API_KEY = getenv("API_KEY");
$API_SECRET = getenv("API_SECRET");

# Setup SDK
$vmc = new VerifyMyContent($API_KEY, $API_SECRET);
$vmc->useSandbox();


// Create Identity Verification
$request = new CreateIdentityVerificationRequest([
    "customer" => [
        "id" => "example-php",
        "email" => "example-php@verifymycontent.com",
    ],
    "redirect_uri" => "",
    "webhook" => ""
]);
$createdVerification = $vmc->identityVerification()->createIdentityVerification($request);

echo "To verify, please access: {$createdVerification->redirect_uri}\n\n";

// Wait until verification is approved
while (true) {
    echo "Checking status...";
    $response = $vmc->identityVerification()->getIdentityVerification($createdVerification->id);
    if ($response->status == IdentityVerificationStatus::APPROVED) {
        echo "\n\nVerification {$createdVerification->id} for user {$createdVerification->customer->id} is approved!\n";
        break;
    }

    echo " " . $response->status . "\n";
    sleep(5);
}
