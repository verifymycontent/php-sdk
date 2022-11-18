<?php

use VerifyMyContent\SDK\IdentityVerification\Entity\Requests\WebhookIdentityVerificationRequest;

require_once __DIR__ . "/../../vendor/autoload.php";

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'POST') {
    http_response_code(405);
    exit;
}

try {
    $body = file_get_contents("php://input");
    $request = new WebhookIdentityVerificationRequest(json_decode($body, true));

    $webhooks = [];
    if (file_exists(__DIR__ . "/webhook.log")) {
        $webhooks = json_decode(file_get_contents(__DIR__ . "/webhook.log"), true);
    }

    file_put_contents("webhook.log", json_encode(array_merge($webhooks, [$request->toArray()])));
    http_response_code(204);
    exit;
}

catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo "Invalid request: " . $e->getMessage();
    exit;
}
catch (Exception $e) {
    http_response_code(400);
    echo "Invalid JSON";
    exit;
}
