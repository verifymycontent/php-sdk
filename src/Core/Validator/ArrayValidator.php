<?php

namespace VerifyMyContent\SDK\Core\Validator;


final class ArrayValidator extends Validator
{
    /**
     * @throws ValidationException
     */
    public static function validate($input, string $field)
    {
        if ($input && !is_array($input)) {
            self::throwValidationException("{$field} must be an array");
        }
    }
}
