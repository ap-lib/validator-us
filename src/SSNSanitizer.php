<?php

namespace AP\Validator\US;

use AP\ErrorNode\Errors;
use AP\Validator\ValidatorInterface;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class SSNSanitizer implements ValidatorInterface
{
    public function __construct(
        public bool   $check_bad_list = true,
        public string $message_format = "invalid ssn format",
        public string $message_bad_list = "sorry, you can not use this ssn",
    )
    {
    }

    private const string REGEX       = '/^(?!666|000|9\d{2})\d{3}(?!00)\d{2}(?!0{4})\d{4}$/';
    private const array  BAD_SSNS_HM = [
        219099999 => true,
        78051120  => true,
        2281852   => true,
        42103580  => true,
        62360749  => true,
        95073645  => true,
        128036045 => true,
        135016629 => true,
        141186941 => true,
        165167999 => true,
        165187999 => true,
        165207999 => true,
        165227999 => true,
        165247999 => true,
        189092294 => true,
        212097694 => true,
        212099999 => true,
        306302348 => true,
        308125070 => true,
        468288779 => true,
        549241889 => true,
    ];

    final public function validate(mixed &$val): true|Errors
    {
        if (is_string($val)) {
            $val = (int)preg_replace(
                "/[^\\d]/",
                "",
                $val
            );
        } elseif (!is_int($val)) {
            return Errors::one("must be a string or integer");
        }

        if (!preg_match(
            self::REGEX,
            sprintf("%09d", $val)
        )) {
            return Errors::one($this->message_format);
        }

        if (isset(self::BAD_SSNS_HM[$val])) {
            return Errors::one($this->message_bad_list);
        }

        return true;
    }

}