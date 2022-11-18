<?php

namespace VerifyMyContent\SDK\IdentityVerification\Entity;

use VerifyMyContent\SDK\Core\DTO;

/**
 * Class Customer
 * @package VerifyMyContent\SDK\IdentityVerification\Entity
 * @property-read string $id
 * @property-read string $email
 */
final class Customer extends DTO
{
    protected $required = ['id', 'email'];
}
