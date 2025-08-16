<?php declare(strict_types=1);

namespace AP\Validator\US\Tests;

use AP\ErrorNode\Errors;
use AP\Validator\US\PhoneSanitizer;
use AP\Validator\US\SSNSanitizer;
use PHPUnit\Framework\TestCase;

final class PhoneSanitizerTest extends TestCase
{

    public function validation_good(int $expected, mixed $actual)
    {
        $validationResult = (new PhoneSanitizer())->validate($actual);
        $this->assertTrue($validationResult);
        $this->assertEquals($expected, $actual);
    }

    public function validation_error(mixed $actual)
    {
        $validationResult = (new PhoneSanitizer)->validate($actual);
        $this->assertInstanceOf(Errors::class, $validationResult);
    }

    public function sanitize_good(int $expected, mixed $actual)
    {
        $validationResult = (new PhoneSanitizer())->sanitize($actual);
        $this->assertTrue($validationResult);
        $this->assertEquals($expected, $actual);
    }

    public function sanitize_error(mixed $actual)
    {
        $validationResult = (new PhoneSanitizer)->sanitize($actual);
        $this->assertInstanceOf(Errors::class, $validationResult);
    }

    public function testValidation()
    {
        $this->validation_good(223_456_7890, 223_456_7890);
        $this->validation_good(223_456_7890, 1_223_456_7890); // long but us code removed
        $this->validation_good(223_456_7890, "2234567890"); // string
        $this->validation_good(223_456_7890, "(223) 456-7890"); // formatted
        $this->validation_good(223_456_7890, "+1 (223) 456-7890"); // long but us code removed str version

        $this->validation_error(2_223_456_7890); // no us and to long
        $this->validation_error(23_456_7890); // short
        $this->validation_error(231_156_7890); // bad NXX 1
        $this->validation_error(231_911_7890); // bad NXX 2
        $this->validation_error(131_911_7890); // bad NPA
        $this->validation_error("0319117890"); // bad NPA str version
        $this->validation_error("(131) 911.7890"); // bad NPA str version formatted

    }

    public function testSanitize()
    {
        $this->sanitize_good(1234, 1234);
        $this->sanitize_good(1234, "1234");

        $this->sanitize_error(null); // bad format
        $this->sanitize_error(["hello world"]); // from bad list
    }
}
