<?php

namespace VerifyMyContent\SDK\IdentityVerification\Entity\Requests;

use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\IdentityVerification\Entity\Customer;

/**
 * Class CreateIdentityCheckRequest
 * @package VerifyMyContent\SDK\IdentityVerification\Entity\Requests
 * @property-read Customer $customer
 * @property-read string $redirect_uri
 * @property-read string $webhook
 */
final class CreateIdentityVerificationRequest extends DTO
{
    protected $required = ['customer', 'redirect_uri', 'webhook'];

    protected $casts = [
        'customer' => Customer::class
    ];
}
