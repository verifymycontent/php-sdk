<?php

namespace VerifyMyContent\SDK\Core\Casts;

use DateTime as BuiltInDateTime;
use DateTimeZone;
use VerifyMyContent\SDK\Core\Validator\ValidationException;

final class DateTime extends BuiltInDateTime
{
    public function __construct($time){
        if (!$time){
            parent::__construct();
            return;
        }

        try {
            parent::__construct($time, new DateTimeZone('UTC'));
        }catch (\Exception $e) {
            throw new ValidationException("{$time} is not a valid date");
        }
    }
}
