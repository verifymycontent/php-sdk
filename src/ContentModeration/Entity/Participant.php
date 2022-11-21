<?php

namespace VerifyMyContent\SDK\ContentModeration\Entity;

use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Entity\Document;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\EmailValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;

/**
 * Class Participant
 * @package VerifyMyContent\SDK\ContentModeration\Entity
 * @property-read string $id
 * @property-read Document $document
 * @property-read string $face
 */
final class Participant extends DTO
{
    protected $fillable = ['id', 'document', 'face'];

    protected $validate = [
        'id' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'document' => [
            ArrayValidator::class,
        ],
        'face' => [
            StringValidator::class,
        ],
    ];

    protected $casts = [
        'document' => Document::class,
    ];
}
