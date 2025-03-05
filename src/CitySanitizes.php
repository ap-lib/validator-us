<?php

namespace AP\Validator\US;

use AP\ErrorNode\Errors;
use AP\Validator\String\AbstractString;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class CitySanitizes extends AbstractString
{
    /**
     * @param string $message Error message displayed when validation fails.
     */
    public function __construct(
        public string $message = "value is not a valid city name",
    )
    {
    }

    /**
     * Sanitizes a city
     *
     * @param string $str The input string to be validated and modified.
     * @return true|Errors Returns `true` if valid, or an `Errors` instance if the resulting string is empty.
     */
    final public function validateString(string &$str): true|Errors
    {
        $str = preg_replace(
            [
                '/`/u',                  // Convert backticks ` to an ASCII apostrophe `'`
                '/[^\p{Latin}\',.-]+/u', // Replace non-Latin characters except `'`, `-`, `.`, and `,` with spaces
                '/\s+/',                 // Collapse multiple spaces into a single space
                '/-+/',                  // Collapse multiple dashes `--`, `---` into a single dash `-`
                '/\'+/',                 // Collapse multiple apostrophes `''`, `'''` into a single apostrophe `'`
                '/\.+/',                 // Collapse multiple apostrophes `..` into a single apostrophe `.`
                '/,+/',                  // Collapse multiple apostrophes `,,`, `,,,` into a single apostrophe `,`
                '/[\'.,-]{2,}/',         // Remove repeated occurrences of apostrophe/dash/dot/comma combinations
                '/\s?([\'-])\s?/',       // Ensure dashes/apostrophes attach directly to words, fix "De' Andra" → "De'Andra"
            ],
            [
                "'",
                ' ',
                ' ',
                '-',
                "'",
                ".",
                ",",
                ".",
                '$1',
            ],
            $str,
        );

        // Trim leading/trailing spaces, dashes, apostrophes, dots, and commas after truncating to 32 characters.
        // The 32-character limit is set based on real-world user input data, where the longest known U.S. city name
        // is "City of the Village of Clarkston"
        $str = trim(
            mb_substr($str, 0, 32),
            " -',."
        );

        return $str === ''
            ? Errors::one($this->message)
            : true;
    }
}