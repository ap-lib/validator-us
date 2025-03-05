<?php

namespace AP\Validator\US;

use AP\ErrorNode\Errors;
use AP\Validator\String\AbstractString;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class AddressSanitizes extends AbstractString
{
    /**
     * @param int $min_length Minimum allowed length for an address.
     * @param string $message Error message displayed when validation fails.
     */
    public function __construct(
        public int    $min_length = 3,
        public int    $max_length = 64,
        public string $message = "value is not a valid street address",
    )
    {
    }

    /**
     * Sanitizes a street address.
     *
     * @param string $str The input string to be validated and modified.
     * @return true|Errors Returns `true` if valid, or an `Errors` instance if the resulting string is too short.
     */
    final public function validateString(string &$str): true|Errors
    {
        $str = preg_replace(
            [
                '/`/u',                                 // Convert backticks ` to a standard apostrophe `'`
                '~[^\p{Latin}0-9#\',.\-&/():\s]+~u',    // Allow only valid address characters
                '/\s+/',                                // Collapse multiple spaces into a single space
                '/-+/',                        // Collapse multiple dashes `--`, `---` into a single dash `-`
                '/\'+/',                       // Collapse multiple apostrophes `''`, `'''` into a single apostrophe `'`
                '/\.+/',                       // Collapse multiple dots `..` into a single dot `.`
                '/,+/',                        // Collapse multiple commas `,,` into a single comma `,`
                '/\/+/',                       // Collapse multiple slashes `//` into a single slash `/`
                '/:+/',                        // Collapse multiple slashes `::` into a single slash `:`
                '/#+/',                        // Collapse multiple slashes `##` into a single slash `#`
                '/\(+/',                       // Collapse multiple slashes `((` into a single slash `(`
                '/\)+/',                       // Collapse multiple slashes `))` into a single slash `)`
                '/\s?([\'\-\/])\s?/',             // Ensure dashes/apostrophes attach directly to words, fix "De' Andra" → "De'Andra"
                '/\s?([.,):])/',             // Ensure dashes/apostrophes attach directly to words, fix "De' Andra" → "De'Andra"
                '/([(#])\s?/',             // Ensure dashes/apostrophes attach directly to words, fix "De' Andra" → "De'Andra"
            ],
            [
                "'",
                ' ',
                ' ',
                '-',
                "'",
                ".",
                ",",
                "/",
                ":",
                "#",
                "(",
                ")",
                "$1",
                "$1",
                "$1",
            ],
            $str,
        );

        // Trim leading/trailing spaces and special characters after truncating to 64 characters.
        // The 64-character limit is set based on real-world address length standards.
        $str = trim(
            mb_substr($str, 0, $this->max_length),
            " -',.#/(:"
        );

        return strlen($str) < $this->min_length
            ? Errors::one($this->message)
            : true;
    }
}