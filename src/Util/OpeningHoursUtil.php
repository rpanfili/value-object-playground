<?php declare(strict_types=1);

namespace App\Util;

use App\Entity\ValueObject\OpeningHours;
use App\Util\Exception\OpeningHoursParseException;

/**
 * Utility class to manage OpeningHours accordingly to schema.org specs
 * @see https://schema.org/openingHours
 *
 */
final class OpeningHoursUtil
{
    private $weekdays = [
            0 => 'Su',
            1 => 'Mo',
            2 => 'Tu',
            3 => 'We',
            4 => 'Th',
            5 => 'Fr',
            6 => 'Sa'
        ];

    const HOURS_PATTERN =
        "(?:[0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]";

    /**
     * Convert the given strings (representing Intervals) into an array with
     *    key:   day week position (0-Sunday)
     *    value: an array of intervals. Each interval is an array [start, end].
     *           Values are as minutes after midnight.
     *
     * @param  string|string[] Intervals definition
     * @return array<int,array<int[]>> An array of intervals, grouped by day number (0 sunday)
     */
    public function toArray($intervals): array
    {
        if (!is_array($intervals)) {
            $intervals = [$intervals];
        }

        $parsed = array_map([$this, 'parseInterval'], $intervals);

        // mix different time ranges by same week day
        $denormalized = [];
        foreach ($parsed as $singleParsed) {
            foreach ($singleParsed as $day => $interval) {
                $denormalized[$day][] = $interval;
            }
        }

        return array_map([$this, 'mergeIntervals'], $denormalized);
    }

    /**
     * Convert string interval to an array that represent time range for each day of the week
     * @param  string $interval
     * @return array<int,int[]> An array with time range for each day of the week
     */
    protected function parseInterval(string $interval): array
    {
        $hours = $this->initStruct();

        $dayChoices = implode('|', $this->weekdays);
        $daysPattern = '\\s*(?P<days>(?:(?:'.$dayChoices.')\\s*[,\\-]\\s*)*(?:'.$dayChoices.'))';
        $hoursPattern = '(?P<starttime>' . self::HOURS_PATTERN.')\\s*-\\s*(?P<endtime>'.self::HOURS_PATTERN.')\\s*';
        $fullPattern = '/^'.$daysPattern.'(?:\\s+'.$hoursPattern.')?$/';

        if (!\preg_match($fullPattern, $interval, $matches)) {
            throw new OpeningHoursParseException(
                "Invalid format",
                OpeningHoursParseException::INVALID_FORMAT
            );
        }
        
        $nextMidnight = 24*60;

        $start = isset($matches['starttime']) ?
            $this->convertTimeToMinutes($matches['starttime']) :
            //otherwise it's intended all-day
            0;

        $end = isset($matches['endtime']) ?
            $this->convertTimeToMinutes($matches['endtime']) :
            //otherwise it's intended all-day
            $nextMidnight;
            
        /*
         * Location is closed all-day
         * @see ALL-DAY HOURS https://developers.google.com/search/docs/data-types/local-business#business_hours
         */
        if ($start === $end) {
            return $hours;
        }

        $days = $this->dayStringToDayList($matches['days']);
        
        if (count(\array_unique($days)) != count($days)) {
            throw new OpeningHoursParseException(
                "Some day appears multiple times",
                OpeningHoursParseException::INVALID_INTERVAL_SAME_DAY
            );
        }
        
        $daysPosition = array_flip($this->weekdays);
        foreach ($days as $day) {
            /*
             * late night hours
             * @see LATE NIGHT HOURS https://developers.google.com/search/docs/data-types/local-business#business_hours
             */
            if ($end < $start) {
                $hours[($position = $daysPosition[$day])] = [$start, $nextMidnight];
                $hours[($position + 1) % count($this->weekdays)] = [0, $end];
            } else {
                $hours[$daysPosition[$day]] = [$start, $end];
            }
        }

        return $hours;
    }
    
    /**
     * Convert a string representing a list of single days / day ranges to an array of single days
     * @return string[] list of day names
     */
    protected function dayStringToDayList(string $days): array
    {
        $days = str_replace(' ', '', $days);
        $list = [];
        foreach (explode(',', $days) as $day) {
            // not an interval
            if (false === strpos($day, '-')) {
                $list[] = $day;
            } else {
                foreach ($this->dayRangeToDayList(...explode('-', $day)) as $singleDay) {
                    $list[] = $singleDay;
                }
            }
        }
        return $list;
    }
    
    /**
     * @param  string $start Day range start
     * @param  string $end   Day range end
     * @return array         Days interval as array of single days in between
     */
    protected function dayRangeToDayList(string $start, string $end): array
    {
        $daysPosition = array_flip($this->weekdays);

        if ($start === $end) {
            throw new OpeningHoursParseException(
                "Invalid day interval: same day given",
                OpeningHoursParseException::INVALID_INTERVAL_SAME_DAY
            );
        }

        $currentPosition = $daysPosition[$start];
        $range = [];
        do {
            $range[] = $currentDay = $this->weekdays[$currentPosition];
            $currentPosition = ($currentPosition + 1) % count($this->weekdays);
        } while ($currentDay != $end);

        return $range;
    }

    /**
     * Convert time string representation to minutes after midnight
     * @param  string $time time as string
     * @return int          minutes after midnight
     */
    protected function convertTimeToMinutes(string $time): int
    {
        list($hour, $minutes) = explode(':', $time);
        return (int)$hour * 60 + (int)$minutes;
    }

    protected function initStruct(): array
    {
        return array_combine(array_keys($this->weekdays), array_fill(0, count($this->weekdays), []));
    }
    
    
    /**
     * Refactor time ranges unifying contiguous and overlapping intervals
     */
    protected function mergeIntervals(array $intervals): array
    {
        $intervals = array_filter($intervals);
        if (count($intervals)) {
            usort($intervals, function ($a, $b) {
                return $a[0] <=> $b[0];
            });

            $last = null;
            for ($i = 0; $i < count($intervals); $i++) {
                $current = $intervals[$i];
                if (null === $last) {
                    $last = &$intervals[$i];
                    continue;
                }

                if ($current[0] <= $last[1]) {
                    if ($current[1] > $last[1]) {
                        $last[1] = $current[1];
                    }
                    unset($intervals[$i]);
                } else {
                    $last = &$intervals[$i];
                }
            }
        }

        return array_values($intervals);
    }
}
