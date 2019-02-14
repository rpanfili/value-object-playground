<?php declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use App\Validator\Constraints\OpeningHours as OpeningHoursConstraint;
use App\Util\OpeningHoursUtil;
use App\Entity\ValueObject\OpeningHours;
use App\Util\Exception\OpeningHoursParseException;

/**
 * @Annotation
 */
final class OpeningHoursValidator extends ConstraintValidator
{
    private $helper;

    public function __construct(OpeningHoursUtil $helper)
    {
        $this->helper = $helper;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof OpeningHoursConstraint) {
            throw new UnexpectedTypeException($constraint, OpeningHoursConstraint::class);
        }

        if (!$value instanceof OpeningHours) {
            throw new UnexpectedValueException($value, OpeningHours::class);
        }

        try {
            $this->helper->toArray($value->getIntervals());
        } catch (OpeningHoursParseException $e) {
            /** @var OpeningHoursConstraint $constraint */
            $code = $constraint::INVALID_FORMAT;
            switch ($e->getCode()) {
                case $e::INVALID_TIME_INTERVAL_LIMITS:
                    $code = $constraint::INVALID_TIME_INTERVAL_LIMITS;
                    break;
                case $e::INVALID_INTERVAL_SAME_DAY:
                    $code = $constraint::INVALID_INTERVAL_SAME_DAY;
                    break;
                case $e::INVALID_FORMAT:
                default:
                    // do nothing
            }

            $this->context->buildViolation($constraint->message)
                ->setCode($code)
                ->addViolation();
        }
    }
}
