<?php

namespace VerifyMyContent\SDK\Complaint\Entity\Responses;

use VerifyMyContent\SDK\Complaint\Entity\ComplaintContent;
use VerifyMyContent\SDK\Complaint\Entity\Content;
use VerifyMyContent\SDK\Complaint\Entity\Customer;
use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;
use VerifyMyContent\SDK\Core\Validator\UrlValidator;

/**
 * Class CreateConsentComplaintRequest
 * @package VerifyMyContent\SDK\Complaint\Entity\Responses
 * @property-read string $id
 * @property-read string $redirect_url
 * @property-read string $status
 */

final class CreateConsentComplaintResponse extends DTO
{
    protected $fillable = ['id', 'redirect_url', 'status'];

    protected $validate = [
        'id' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'redirect_url' => [
            RequiredValidator::class,
            UrlValidator::class,
        ],
        'status' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
    ];
}
