<?php

namespace VerifyMyContent\SDK\ContentModeration\Entity\Responses;

use VerifyMyContent\SDK\ContentModeration\Entity\Participant;
use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;

/**
 * Class GetStaticContentModerationParticipantsResponse
 * @package VerifyMyContent\SDK\ContentModeration\Entity\Responses
 * @property-read string $id
 * @property-read Participant $customer
 * @property string $face
 */
final class GetStaticContentModerationParticipantsResponse extends DTO
{
    protected $fillable = ['id', 'customer', 'face'];

    protected $validate = [
        'id' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'customer' => [
            RequiredValidator::class,
            ArrayValidator::class,
        ],
        'face' => [
            StringValidator::class,
        ],
    ];

    protected $casts = [
        'customer' => Participant::class,
    ];
}
