<?php declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class OpeningHours extends Constraint
{
    const INVALID_FORMAT = 'd73370ad-5477-4e21-a4ed-9832163ac5ad';
    const INVALID_TIME_INTERVAL_LIMITS = 'e702da56-acd6-4e6f-93e5-7fb0e8254b72';
    const INVALID_INTERVAL_SAME_DAY = 'a7ba26b6-4acf-4594-ad97-407d0a5bf8dd';

    protected static $errorNames = [
        self::INVALID_FORMAT => 'INVALID_FORMAT',
        self::INVALID_TIME_INTERVAL_LIMITS => 'INVALID_TIME_INTERVAL_LIMITS',
        self::INVALID_INTERVAL_SAME_DAY => 'INVALID_INTERVAL_SAME_DAY',
    ];

    public $message = 'This value is not a valid OpeningHour.';
}
