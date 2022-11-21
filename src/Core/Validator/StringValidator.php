<?php

namespace VerifyMyContent\SDK\Core\Validator;


final class StringValidator extends Validator
{
    /**
     * @throws ValidationException
     */
    public static function validate($input, string $field)
    {
        if (!is_null($input) && !is_string($input)) {
            self::throwValidationException("{$field} must be a string");
        }
    }
}