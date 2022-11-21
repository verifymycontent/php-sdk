<?php

namespace VerifyMyContent\SDK\Complaint\Entity\Responses;

use VerifyMyContent\SDK\Complaint\Entity\Customer;
use VerifyMyContent\SDK\Complaint\Entity\Stream;
use VerifyMyContent\SDK\Core\Casts\DateTime;
use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;
use VerifyMyContent\SDK\Core\Validator\UrlValidator;

/**
 * Class CreateLiveContentComplaintResponse
 * @package VerifyMyContent\SDK\Complaint\Entity\Responses
 * @property-read string $id
 * @property-read string $external_id
 * @property-read string $notes
 * @property-read string $status
 * @property-read string[] $tags
 * @property-read DateTime $created_at
 * @property-read DateTime $updated_at
 */
final class CreateLiveContentComplaintResponse extends DTO
{
    protected $fillable = ['external_id', 'id', 'notes', 'status', 'tags', 'created_at', 'updated_at'];

    protected $validate = [
        'external_id' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'id' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'notes' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'status' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'tags' => [
            RequiredValidator::class,
            ArrayValidator::class,
        ],
        'created_at' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'updated_at' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
    ];

    protected $casts = [
        'created_at' => DateTime::class,
        'updated_at' => DateTime::class,
    ];
}
