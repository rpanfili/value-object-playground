<?php declare(strict_types = 1);

namespace App\Util\Exception;

class OpeningHoursParseException extends \InvalidArgumentException
{
    const INVALID_FORMAT = 100;
    const INVALID_INTERVAL_SAME_DAY = 30;
}
