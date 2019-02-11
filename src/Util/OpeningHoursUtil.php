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
    private
        $weekdays = [
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
     * @param  string|string[] Intervals definition
     * @return array
     */
    public function parse($value): array
    {
        if(!is_array($value))
        {
            $value = [$value];
        }

        $parsed = array_map([$this, 'parseRow'], $value);

        $denormalized = [];
        foreach($parsed as $singleParsed)
        {
            foreach($singleParsed as $day => $interval)
            {
                $denormalized[$day][] = $interval;
            }
        }

        return array_map([$this, 'mergeIntervals'], $denormalized);
    }

    public function format(OpeningHours $openingHours)
    {
        $intervals = $openingHours->getIntervals();
        $encoded = null;
        if(!empty($intervals))
        {
            $encoded = serialize($encoded);
        }
        return $encoded;
    }

    protected function mergeIntervals($intervals): array
    {
        $intervals = array_filter($intervals);
        if(count($intervals))
        {
            usort($intervals, function($a, $b){
                return $a[0] <=> $b[0];
            });

            $last = null;
            for($i = 0; $i < count($intervals); $i++)
            {
                $current = $intervals[$i];
                if(null === $last)
                {
                    $last = &$intervals[$i];
                    continue;
                }

                if($current[0] <= $last[1])
                {
                    if($current[1] > $last[1])
                    {
                        $last[1] = $current[1];
                    }
                    unset($intervals[$i]);
                }
                else
                {
                    $last = &$intervals[$i];
                }
            }
        }

        return array_values($intervals);
    }

    protected function parseRow(string $value): array
    {
        $hours = $this->initStruct();

        $days_choices = implode('|', $this->weekdays);
        $days_pattern = '\\s*(?P<days>(?:(?:'.$days_choices.')\\s*[,\\-]\\s*)*(?:'.$days_choices.'))';
        $hours_pattern = '(?P<starttime>' . self::HOURS_PATTERN.')\\s*-\\s*(?P<endtime>'.self::HOURS_PATTERN.')\\s*';
        $full_pattern = '/^'.$days_pattern.'(?:\\s+'.$hours_pattern.')?$/';

        if(!\preg_match($full_pattern, $value, $matches))
        {
            throw new OpeningHoursParseException(
                "Invalid format",
                OpeningHoursParseException::INVALID_FORMAT
            );
        }

        $start = isset($matches['starttime']) ?
            $this->convertTimeToMinutes($matches['starttime']) :
            //otherwise it's intended all-day
            0;

        $end = isset($matches['endtime']) ?
            $this->convertTimeToMinutes($matches['endtime']) :
            //otherwise it's intended all-day
            (24*60);

        if($start >= $end)
        {
            throw new OpeningHoursParseException(
                "Invalid interval delimiters: it cannot begin after its conclusion",
                OpeningHoursParseException::INVALID_TIME_INTERVAL_LIMITS
            );
        }

        $days = str_replace(' ', '', trim($matches['days']));
        $days_position = array_flip($this->weekdays);

        // convert day ranges
        if(false !== strpos($days, '-'))
        {
            $days_pattern = implode('|', $this->weekdays);
            $days = preg_replace_callback(
                '/('.$days_pattern.')\-('.$days_pattern.')/',
                function($matches) use ($days_position){

                    $start = $matches[1];
                    $end = $matches[2];

                    if($start === $end)
                    {
                        throw new OpeningHoursParseException(
                            "Invalid day interval: same day given",
                            OpeningHoursParseException::INVALID_INTERVAL_SAME_DAY
                        );
                    }

                    $current_position = $days_position[$start];
                    $range = [];
                    do
                    {
                        $range[] = $current_day = $this->weekdays[$current_position];
                        $current_position = ($current_position + 1) % count($this->weekdays);
                    }
                    while($current_day != $end);

                    return implode(',', $range);
                },
                $days
            );
        }

        if(!empty($days))
        {
            $single_days = explode(',',$days);
            if(count(\array_unique($single_days)) != count($single_days))
            {
                throw new OpeningHoursParseException(
                    "Some day appears multiple times",
                    OpeningHoursParseException::INVALID_INTERVAL_SAME_DAY
                );
            }

            foreach($single_days as $day)
            {
                if(!\array_key_exists($day, $days_position))
                {
                    throw new OpeningHoursParseException(
                        "Day `$day` is invalid. Accepted values are: ".
                            implode(', ', $this->weekdays),
                        OpeningHoursParseException::INVALID_DAY_NAME
                    );
                }

                $hours[$days_position[$day]] = [$start, $end];
            }
        }

        return $hours;
    }

    protected function convertTimeToMinutes(string $time): int
    {
        list($hour, $minutes) = explode(':', $time);
        return (int)$hour * 60 + (int)$minutes;
    }

    protected function initStruct(): array
    {
        return array_combine(array_keys($this->weekdays), array_fill(0, count($this->weekdays), []));
    }
}
