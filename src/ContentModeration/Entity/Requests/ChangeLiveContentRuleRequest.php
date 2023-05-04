<?php

namespace VerifyMyContent\SDK\ContentModeration\Entity\Requests;

use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;

final class ChangeLiveContentRuleRequest extends DTO
{
    protected $fillable = ['rule'];

    protected $validate = [
        'rule' => [
            RequiredValidator::class,
            StringValidator::class
        ]
    ];
}