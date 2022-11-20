<?php

namespace VerifyMyContent\SDK\Core\Casts;

use DateTime;
use DateTimeZone;
use VerifyMyContent\SDK\Core\Validator\ValidationException;

final class DateTimeCast extends DateTime
{
    public function __construct($time){
        try {
            parent::__construct($time, new DateTimeZone('UTC'));
        }catch (\Exception $e) {
            throw new ValidationException("{$time} is not a valid date");
        }
    }
}
