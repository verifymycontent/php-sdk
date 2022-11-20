<?php

namespace VerifyMyContent\SDK\IdentityVerification\Entity\Requests;

use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;

/**
 * Class WebhookIdentityVerificationRequest
 * @package VerifyMyContent\SDK\IdentityVerification\Entity\Requests
 * @property-read string $id
 * @property-read string $customer_id
 * @property-read string $status
 */
final class WebhookIdentityVerificationRequest extends DTO
{
    protected $fillable = ['id', 'customer_id', 'status'];

    protected $validate = [
        'id' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'customer_id' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'status' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
    ];
}
