<?php

namespace test_is74\Helpers;

use \DateTime;

class DateTimeHelper
{
    const FORMAT = "d.m.Y";
    const FORMAT_TIME = "d.m.Y H:i:s";

    public static function validateDate(string $date) : bool
    {
        $dateTime = DateTime::createFromFormat(self::FORMAT, $date);
        return $dateTime && $dateTime->format(self::FORMAT) == $date;
    }

    public static function validateDateTime(string $date) : bool
    {
        $dateTime = DateTime::createFromFormat(self::FORMAT_TIME, $date);
        return $dateTime && $dateTime->format(self::FORMAT_TIME) == $date;
    }

    public static function dateFromTimestamp(int $timestamp) : string
    {
        return date(self::FORMAT, $timestamp);
    }

    public static function dateTimeFromTimestamp(int $timestamp) : string
    {
        return date(self::FORMAT_TIME, $timestamp);
    }

    public static function toTimestamp(string $date_or_dateTime) : int
    {
        $isDateTime = false;
        if (!self::validateDate($date_or_dateTime))
        {
            $isDateTime = true;
            if (!self::validateDateTime($date_or_dateTime))
            {
                return 0;
            }
        }

        if ($isDateTime)
        {
            $dateTime = DateTime::createFromFormat(self::FORMAT_TIME, $date_or_dateTime);
        }
        else
        {
            $dateTime = DateTime::createFromFormat(self::FORMAT, $date_or_dateTime);
        }
        return $dateTime->getTimestamp();
    }

    public static function getCurrentDateTime() : string
    {
        return date(self::FORMAT_TIME);
    }

    public static function getCurrentDate() : string
    {
        return date(self::FORMAT);
    }
}