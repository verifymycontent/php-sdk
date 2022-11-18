<?php

namespace VerifyMyContent\SDK\IdentityVerification\Entity\Requests;

use VerifyMyContent\SDK\Core\DTO;

/**
 * Class WebhookIdentityVerificationRequest
 * @package VerifyMyContent\SDK\IdentityVerification\Entity\Requests
 * @property-read string $id
 * @property-read string $customer_id
 * @property-read string $status
 */
final class WebhookIdentityVerificationRequest extends DTO
{
    protected $required = ['id', 'customer_id', 'status'];
}
