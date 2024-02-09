<?php

namespace Lib\Support;

use DateTime;
use DateTimeZone;
use Lib\Exception\CustomException;

/**
 * Class Date
 * 
 * Provides functionality for working with dates, times, and timezones.
 * 
 * @CodeError 23
 */
class Date
{
    /**
     * The now function returns the current date and time.
     *
     * @return DateTime
     */
    public static function now(): DateTime
    {
        try {
            return new DateTime('now', new DateTimeZone(config('app.timezone')));
        } catch (\Exception $e) {
            throw new CustomException(2301, lang('invalid_date_format'), lang('invalid_date_format_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * The today function returns the current date.
     *
     * @return DateTime
     */
    public static function today(): DateTime
    {
        try {
            return self::now();
        } catch (\Exception $e) {
            throw new CustomException(2302, lang('get_today_error'), lang('get_today_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * The tomorrow function returns the date of tomorrow.
     *
     * @return DateTime
     */
    public static function tomorrow(): DateTime
    {
        try {
            return self::today()->modify('+1 day');
        } catch (\Exception $e) {
            throw new CustomException(2303, lang('get_tomorrow_error'), lang('get_tomorrow_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * The yesterday function returns the date of yesterday.
     *
     * @return DateTime
     */
    public static function yesterday(): DateTime
    {
        try {
            return self::today()->modify('-1 day');
        } catch (\Exception $e) {
            throw new CustomException(2304, lang('get_yesterday_error'), lang('get_yesterday_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Check if a given date is a weekend.
     *
     * @param DateTime $date The date to check.
     *
     * @return bool True if the date is a weekend, false otherwise.
     */
    public static function isWeekend(DateTime $date): bool
    {
        try {
            $date = self::setTimeZone($date);

            if (!$date) {
                return false;
            }

            $dayOfWeek = (int)$date->format('N');

            return $dayOfWeek === 6 || $dayOfWeek === 7;
        } catch (\Exception $e) {
            throw new CustomException(2305, lang('is_weekend_error'), lang('is_weekend_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Add a specified number of days to a date.
     *
     * @param DateTime $date The initial date.
     * @param int      $days The number of days to add.
     *
     * @return DateTime The modified date.
     */
    public static function addDays(DateTime $date, int $days): DateTime
    {
        try {
            $date = self::setTimeZone($date);

            return $date->modify("+$days days");
        } catch (\Exception $e) {
            throw new CustomException(2306, lang('add_days_error'), lang('add_days_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Subtract a specified number of days from a date.
     *
     * @param DateTime $date The initial date.
     * @param int      $days The number of days to subtract.
     *
     * @return DateTime The modified date.
     */
    public static function subDays(DateTime $date, int $days): DateTime
    {
        try {
            $date = self::setTimeZone($date);

            return $date->modify("-$days days");
        } catch (\Exception $e) {
            throw new CustomException(2307, lang('sub_days_error'), lang('sub_days_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Add a specified number of weeks to a date.
     * 
     * @param DateTime $date The initial date.
     * @param int      $weeks The number of weeks to add.
     * 
     * @return DateTime The modified date.
     */
    public function addWeeks(DateTime $date, int $weeks): DateTime
    {
        try {
            $date = self::setTimeZone($date);

            return $date->modify("+$weeks weeks");
        } catch (\Exception $e) {
            throw new CustomException(2308, lang('add_weeks_error'), lang('add_weeks_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Subtract a specified number of weeks from a date.
     * 
     * @param DateTime $date The initial date.
     * @param int      $weeks The number of weeks to subtract.
     * 
     * @return DateTime The modified date.
     */
    public function subWeeks(DateTime $date, int $weeks): DateTime
    {
        try {
            $date = self::setTimeZone($date);

            return $date->modify("-$weeks weeks");
        } catch (\Exception $e) {
            throw new CustomException(2309, lang('subtract_weeks_error'), lang('subtract_weeks_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Add a specified number of months to a date.
     * 
     * @param DateTime $date The initial date.
     * @param int      $months The number of months to add.
     * 
     * @return DateTime The modified date.
     */
    public static function addMonths(DateTime $date, int $months): DateTime
    {
        try {
            $date = self::setTimeZone($date);

            return $date->modify("+$months months");
        } catch (\Exception $e) {
            throw new CustomException(2310, lang('add_months_error'), lang('add_months_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Subtract a specified number of months from a date.
     * 
     * @param DateTime $date The initial date.
     * @param int      $months The number of months to subtract.
     * 
     * @return DateTime The modified date.
     */
    public static function subMonths(DateTime $date, int $months): DateTime
    {
        try {
            $date = self::setTimeZone($date);

            return $date->modify("-$months months");
        } catch (\Exception $e) {
            throw new CustomException(2311, lang('subtract_months_error'), lang('subtract_months_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Add a specified number of years to a date.
     * 
     * @param DateTime $date The initial date.
     * @param int      $years The number of years to add.
     * 
     * @return DateTime The modified date.
     */
    public static function addYears(DateTime $date, int $years): DateTime
    {
        try {
            $date = self::setTimeZone($date);

            return $date->modify("+$years years");
        } catch (\Exception $e) {
            throw new CustomException(2312, lang('add_years_error'), lang('add_years_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Subtract a specified number of years from a date.
     * 
     * @param DateTime $date The initial date.
     * @param int      $years The number of years to subtract.
     * 
     * @return DateTime The modified date.
     */
    public static function subYears(DateTime $date, int $years): DateTime
    {
        try {
            $date = self::setTimeZone($date);

            return $date->modify("-$years years");
        } catch (\Exception $e) {
            throw new CustomException(2313, lang('subtract_years_error'), lang('subtract_years_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Add a specified number of hours to a date.
     * 
     * @param DateTime $date The initial date.
     * @param int      $hours The number of hours to add.
     * 
     * @return DateTime The modified date.
     */
    public static function addHours(DateTime $date, int $hours): DateTime
    {
        try {
            $date = self::setTimeZone($date);

            return $date->modify("+$hours hours");
        } catch (\Exception $e) {
            throw new CustomException(2314, lang('add_hours_error'), lang('add_hours_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Subtract a specified number of hours from a date.
     * 
     * @param DateTime $date The initial date.
     * @param int      $hours The number of hours to subtract.
     * 
     * @return DateTime The modified date.
     */
    public static function subHours(DateTime $date, int $hours): DateTime
    {
        try {
            $date = self::setTimeZone($date);

            return $date->modify("-$hours hours");
        } catch (\Exception $e) {
            throw new CustomException(2315, lang('subtract_hours_error'), lang('subtract_hours_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Add a specified number of minutes to a date.
     * 
     * @param DateTime $date The initial date.
     * @param int      $minutes The number of minutes to add.
     * 
     * @return DateTime The modified date.
     */
    public static function addMinutes(DateTime $date, int $minutes): DateTime
    {
        try {
            $date = self::setTimeZone($date);

            return $date->modify("+$minutes minutes");
        } catch (\Exception $e) {
            throw new CustomException(2316, lang('add_minutes_error'), lang('add_minutes_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Subtract a specified number of minutes from a date.
     * 
     * @param DateTime $date The initial date.
     * @param int      $minutes The number of minutes to subtract.
     * 
     * @return DateTime The modified date.
     */
    public static function subMinutes(DateTime $date, int $minutes): DateTime
    {
        try {
            $date = self::setTimeZone($date);

            return $date->modify("-$minutes minutes");
        } catch (\Exception $e) {
            throw new CustomException(2317, lang('subtract_minutes_error'), lang('subtract_minutes_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Add a specified number of seconds to a date.
     * 
     * @param DateTime $date The initial date.
     * @param int      $seconds The number of seconds to add.
     * 
     * @return DateTime The modified date.
     */
    public static function addSeconds(DateTime $date, int $seconds): DateTime
    {
        try {
            $date = self::setTimeZone($date);

            return $date->modify("+$seconds seconds");
        } catch (\Exception $e) {
            throw new CustomException(2318, lang('add_seconds_error'), lang('add_seconds_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Subtract a specified number of seconds from a date.
     * 
     * @param DateTime $date The initial date.
     * @param int      $seconds The number of seconds to subtract.
     * 
     * @return DateTime The modified date.
     */
    public static function subSeconds(DateTime $date, int $seconds): DateTime
    {
        try {
            $date = self::setTimeZone($date);

            return $date->modify("-$seconds seconds");
        } catch (\Exception $e) {
            throw new CustomException(2319, lang('subtract_seconds_error'), lang('subtract_seconds_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * The format function returns true if the date is a weekday.
     * 
     * @param DateTime $date
     * @param string   $format
     * 
     * @return bool
     * */
    public static function format(DateTime $date, string $format): string
    {
        try {
            $date = self::setTimeZone($date);

            if (strlen($format) === 0) {
                throw new \Exception(lang('date_format_empty'));
            }

            return $date->format($format);
        } catch (\Exception $e) {
            throw new CustomException(2320, lang('invalid_date_format'), lang('invalid_date_format_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Count the number of days between two dates.
     * 
     * @param DateTime $start
     * @param DateTime $end
     * 
     * @return int
     */
    public static function diffInDays(DateTime $start, DateTime $end): int
    {
        $start = self::setTimeZone($start);
        $end = self::setTimeZone($end);

        if ($start > $end) {
            throw new CustomException(2321, lang('invalid_date_range'), lang('invalid_date_range_message'));
        }

        $diff = $end->diff($start);
        return (int)$diff->format('%a');
    }

    /**
     * Count the number of weekdays between two dates.
     *
     * @param DateTime $startDate The start date.
     * @param DateTime $endDate   The end date.
     * @param array    $weekdays  Optional. An array of weekday numbers to consider as weekdays.
     *
     * @return int The number of weekdays between the two dates.
     */
    public static function countWeekdays(DateTime $startDate, DateTime $endDate, array $weekdays = [1, 2, 3, 4, 5]): int
    {
        try {
            $startDate = self::setTimeZone($startDate);
            $endDate = self::setTimeZone($endDate);

            if ($startDate > $endDate) {
                throw new \Exception('Start date must be before end date.');
            }

            $weekdays = array_unique(array_map('intval', $weekdays));
            sort($weekdays);

            $weekdaysCount = 0;
            $currentDate = clone $startDate;

            while ($currentDate <= $endDate) {
                if (in_array((int)$currentDate->format('N'), $weekdays)) {
                    $weekdaysCount++;
                }

                $currentDate->modify('+1 day');
            }

            return $weekdaysCount;
        } catch (\Exception $e) {
            throw new CustomException(2322, lang('count_weekdays_error'), lang('count_weekdays_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Get the current timestamp.
     * 
     * @return int
     * */
    public static function timestamps()
    {
        try {
            return time();
        } catch (\Exception $e) {
            throw new CustomException(2323, lang('get_timestamps_error'), lang('get_timestamps_error_message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Set the timezone of a date.
     * 
     * @param DateTime $date
     * 
     * @return DateTime
     * */
    private static function setTimeZone(DateTime $date): DateTime
    {
        try {
            return $date->setTimezone(new DateTimeZone(config('app.timezone')));
        } catch (\Exception $e) {
            throw new CustomException(2324, lang('invalid_timezone'), lang('invalid_timezone_message', ['message' => $e->getMessage()]));
        }
    }
}
