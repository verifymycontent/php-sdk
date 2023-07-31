<?php

namespace VerifyMyContent\SDK\ContentModeration\Entity;

use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;
use VerifyMyContent\SDK\Core\Validator\UrlValidator;

/**
 * Class Content
 * @package VerifyMyContent\SDK\ContentModeration\Entity
 * @property-read string $protocol
 * @property-read string $url
 */
final class Stream extends DTO
{
    protected $fillable = ['protocol', 'url'];
}
