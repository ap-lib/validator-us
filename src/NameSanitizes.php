<?php

namespace AP\Validator\US;

use AP\ErrorNode\Errors;
use AP\Validator\String\AbstractString;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class NameSanitizes extends AbstractString
{
    /**
     * @param string $message Error message displayed when validation fails.
     */
    public function __construct(
        public string $message = "value is not a valid name",
    )
    {
    }

    /**
     * Sanitizes a name
     *
     * @param string $str The input string to be validated and modified.
     * @return true|Errors Returns `true` if valid, or an `Errors` instance if the resulting string is empty.
     */
    final public function validateString(string &$str): true|Errors
    {
        $str = preg_replace(
            [
                '/`/u',                 // Convert backticks ` to an ASCII apostrophe `'`
                '/[^\p{Latin}\'-]+/u',  // Replace non-alphabetic characters except `'` and `-` with spaces
                '/\s+/',                // Collapse multiple spaces into a single space
                '/-+/',                 // Collapse multiple dashes `--`, `---` into a single dash `-`
                '/\'+/',                // Collapse multiple apostrophes `''`, `'''` into a single apostrophe `'`
                '/[\'\-]{2,}/',         // Remove repeated occurrences of apostrophe/dash combinations
                '/\s?([\'-])\s?/',      // Ensure dashes/apostrophes attach directly to words, fix "De' Aundra" → "De'Aundra"
            ],
            [
                "'",
                ' ',
                ' ',
                '-',
                "'",
                "-",
                '$1',
            ],
            $str,
        );

        // Trim leading/trailing spaces, dashes, and apostrophes after truncating to 32 characters.
        // The limit is set to 32 because users may enter multiple names in a single form field.
        // Observations show that name combinations typically don't exceed 32 characters.
        $str = trim(
            mb_substr($str, 0, 32),
            " -'"
        );

        return $str === ''
            ? Errors::one($this->message)
            : true;
    }
}