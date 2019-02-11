<?php declare(strict_types = 1);

namespace App\Util\Exception;

class OpeningHoursParseException extends \InvalidArgumentException
{
    const INVALID_FORMAT = 100;
    const INVALID_DAY_NAME = 10;
    const INVALID_TIME_INTERVAL_LIMITS = 20;
    const INVALID_INTERVAL_SAME_DAY = 30;
}
