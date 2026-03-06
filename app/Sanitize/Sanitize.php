<?php

namespace App\Sanitize;

class Sanitize
{
    /**
     * Summary of string
     * @param mixed $value
     * @return string
     */
    public static function string(mixed $value): string
    {
        return htmlspecialchars(strip_tags(trim($value ?? ''))  );
    }

    /**
     * Summary of email
     * @param mixed $value
     * @return string
     */
    public static function email(mixed $value): string
    {
        return filter_var(trim($value ?? ''), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Summary of int
     * @param mixed $value
     * @return int
     */
    public static function int(mixed $value): int
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Summary of trim
     * @param mixed $value
     * @return string
     */
    public static function trim(mixed $value): string
    {
        return trim($value ?? '');
    }

    /**
     * Summary of name
     * @param mixed $value
     * @return array|string|null
     */
    public static function name(mixed $value): string
    {
        $sanitized = htmlspecialchars(trim($value ?? ''), ENT_QUOTES, 'UTF-8');
        return preg_replace("/[^a-zA-Z\s'\-]/u", '', $sanitized);
    }
}