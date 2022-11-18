<?php

namespace VerifyMyContent\SDK\IdentityVerification\Entity\Responses;

use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\IdentityVerification\Entity\Customer;

/**
 * Class CreateIdentityCheckResponse
 * @package VerifyMyContent\SDK\IdentityVerification\Entity\Responses
 * @property-read string $id
 * @property-read string $redirect_uri
 * @property-read string $webhook
 * @property-read Customer $customer
 */
final class CreateIdentityVerificationResponse extends DTO
{
    protected $fillable = ['id', 'customer', 'redirect_uri', 'webhook'];

    protected $casts = [
        'customer' => Customer::class
    ];
}
