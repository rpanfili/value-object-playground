<?php

declare(strict_types=1);

namespace App\Entity\ValueObject;

/**
 * The general opening hours for a business.
 *
 * @see https://schema.org/openingHours
 */
final class OpeningHours
{
    private $intervals = [];

    /**
     * @param string|string[] $intervals
     */
    public function __construct($intervals = null)
    {
        if (null !== $intervals) {
            $this->intervals = is_array($intervals) ?
                $intervals :
                [$intervals];
        }
    }

    /**
     * @return array An array of strings which define multiple day intervals of opening hours
     */
    public function getIntervals(): array
    {
        return $this->intervals;
    }
}
