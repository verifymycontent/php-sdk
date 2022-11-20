<?php

namespace VerifyMyContent\SDK\Core\Validator;

abstract class Validator
{
    /**
     * @throws ValidationException
     */
    protected static function throwValidationException($message)
    {
        throw new ValidationException($message, 422);
    }

    /**
     * @param $input
     * @param string $field
     * @return void
     * @throws ValidationException
     */
    abstract public static function validate($input, string $field);
}
