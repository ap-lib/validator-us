<?php declare(strict_types=1);

namespace AP\Validator\US\Tests;

use AP\ErrorNode\Errors;
use AP\Validator\US\SSNSanitizer;
use PHPUnit\Framework\TestCase;

final class SSNSanitizerTest extends TestCase
{

    public function good(int $expected, mixed $actual)
    {
        $validationResult = (new SSNSanitizer())->validate($actual);
        $this->assertTrue($validationResult);
        $this->assertEquals($expected, $actual);
    }

    public function error(mixed $actual)
    {
        $validationResult = (new SSNSanitizer)->validate($actual);
        $this->assertInstanceOf(Errors::class, $validationResult);
    }

    public function sanitize_good(int $expected, mixed $actual)
    {
        $validationResult = (new SSNSanitizer())->sanitize($actual);
        $this->assertTrue($validationResult);
        $this->assertEquals($expected, $actual);
    }

    public function sanitize_error(mixed $actual)
    {
        $validationResult = (new SSNSanitizer)->sanitize($actual);
        $this->assertInstanceOf(Errors::class, $validationResult);
    }

    public function testValidation()
    {
        $this->good(123456789, 123456789);
        $this->good(123456789, "123456789");
        $this->good(123456789, "123-45-6789");
        $this->good(123456789, "my ssn is: 123-45-6789");

        $this->good(12345678, 12345678);
        $this->good(12345678, '012345678');
        $this->good(12345678, '000012345678');
        $this->good(12345678, '12-34-5678');

        $this->error('666-77-8888'); // bad format
        $this->error('468-28-8779'); // from bad list
    }

    public function testSanitize()
    {
        $this->sanitize_good(123456789123, 123456789123);
        $this->sanitize_good(123456789123, "123456789123");

        $this->sanitize_error(null); // bad format
        $this->sanitize_error(["hello world"]); // from bad list
    }
}
