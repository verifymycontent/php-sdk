<?php

namespace VerifyMyContent\SDK\ReIdentification\Entity\Responses;

use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;
use VerifyMyContent\SDK\Core\Validator\UrlValidator;
use VerifyMyContent\SDK\ReIdentification\Entity\Customer;

/**
 * Class GetReIdentificationResponse
 * @package VerifyMyContent\SDK\ReIdentification\Entity\Responses
 * @property-read string $id
 * @property-read string $status
 * @property-read string $redirect_uri
 * @property-read string $webhook
 * @property-read Customer $customer
 */
final class GetReIdentificationResponse extends DTO
{
    protected $fillable = ['id', 'customer', 'redirect_uri', 'webhook', 'status'];

    protected $validate = [
        'id' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'customer' => [
            RequiredValidator::class,
            ArrayValidator::class,
        ],
        'redirect_uri' => UrlValidator::class,
        'webhook' => UrlValidator::class,
        'status' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
    ];

    protected $casts = [
        'customer' => Customer::class,
    ];
}
