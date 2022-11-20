<?php

namespace VerifyMyContent\SDK\Core\Validator;


final class EmailValidator extends Validator
{
    /**
     * @throws ValidationException
     */
    public static function validate($input, string $field)
    {
        if (!is_null($input) && !filter_var($input, FILTER_VALIDATE_EMAIL)) {
            self::throwValidationException("{$field} is not a valid email address");
        }
    }
}
