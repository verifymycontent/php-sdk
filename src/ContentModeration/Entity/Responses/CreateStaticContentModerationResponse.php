<?php

namespace VerifyMyContent\SDK\ContentModeration\Entity\Responses;

use VerifyMyContent\SDK\Core\Casts\DateTimeCast;
use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;

/**
 * Class CreateStaticContentModerationResponse
 * @package VerifyMyContent\SDK\ContentModeration\Entity\Responses
 * @property-read string $id
 * @property-read string $redirect_url
 * @property-read string $external_id
 * @property-read string $status
 * @property-read string $notes
 * @property-read array $tags
 * @property-read DateTimeCast $created_at
 * @property-read DateTimeCast $updated_at
 */
final class CreateStaticContentModerationResponse extends DTO
{
    protected $fillable = ['id', 'redirect_url', 'external_id', 'status', 'notes', 'tags', 'created_at', 'updated_at'];

    protected $validate = [
        'id' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'redirect_url' => [
            StringValidator::class,
        ],
        'external_id' => [
            StringValidator::class,
        ],
        'status' => [
            StringValidator::class,
        ],
        'notes' => [
            StringValidator::class,
        ],
        'tags' => [
            ArrayValidator::class,
        ],
        'created_at' => [
            StringValidator::class,
        ],
        'updated_at' => [
            StringValidator::class,
        ],
    ];

    protected $casts = [
        'created_at' => DateTimeCast::class,
        'updated_at' => DateTimeCast::class,
    ];
}
