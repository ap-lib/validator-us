<?php

namespace AP\Validator\US;

use AP\ErrorNode\Errors;
use AP\Validator\ValidatorInterface;
use Attribute;
use RuntimeException;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class PhoneSanitizer implements ValidatorInterface
{
    private const array BAD_NXX_HM = [
        911 => true
    ];

    public function __construct(
        public bool   $remove_international_code = true,
        public bool   $check_by_bad_nxx = true,
        public string $message_invalid_format = "invalid phone format",
        public string $message_invalid_format_npa = "invalid phone format",
        public string $message_invalid_format_nxx = "invalid phone format",
    )
    {
    }

    final public function sanitize(mixed &$val): true|Errors
    {
        if (is_string($val)) {
            $val = (int)preg_replace(
                "/[^\\d]/",
                "",
                $val
            );
        } elseif (!is_int($val)) {
            return Errors::one($this->message_invalid_format);
        }
        return true;
    }

    final public function validate(mixed &$val): true|Errors
    {
        $res = $this->sanitize($val);
        if ($res instanceof Errors) {
            return $res;
        }

        if (!is_int($val)) {
            throw new RuntimeException(
                'post-condition: phone must be converted to int'
            );
        }

        // remove an international code
        if (
            $this->remove_international_code
            && $val > 999_999_9999 &&
            $val < 2_000_000_0000
        ) {
            $val = (int)substr((string)$val, 1);
        }

        // Format
        if ($val < 100_000_0000 || $val > 999_999_9999) {
            return Errors::one($this->message_invalid_format);
        }

        // NPA
        if ($val < 200_000_0000) {
            return Errors::one($this->message_invalid_format_npa);
        }

        // NXX
        $nxx = ($val / 10_000) % 1000;
        if ($nxx < 200) {
            return Errors::one($this->message_invalid_format_nxx);
        }

        if ($this->check_by_bad_nxx && isset(self::BAD_NXX_HM[$nxx])) {
            return Errors::one($this->message_invalid_format_nxx);
        }

        return true;
    }

}