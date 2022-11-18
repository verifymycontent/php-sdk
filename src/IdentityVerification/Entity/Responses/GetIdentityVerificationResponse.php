<?php

namespace VerifyMyContent\SDK\IdentityVerification\Entity\Responses;

use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\IdentityVerification\Entity\Customer;

/**
 * Class GetIdentityCheckResponse
 * @package VerifyMyContent\SDK\IdentityVerification\Entity\Responses
 * @property-read string $id
 * @property-read string $redirect_uri
 * @property-read string $webhook
 * @property-read string $status
 * @property-read Customer $customer
 */
final class GetIdentityVerificationResponse extends DTO
{
    protected $required = ['id', 'customer', 'redirect_uri', 'webhook', 'status'];

    protected $casts = [
        'customer' => Customer::class
    ];
}