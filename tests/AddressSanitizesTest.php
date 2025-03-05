<?php declare(strict_types=1);

namespace AP\Validator\US\Tests;

use AP\ErrorNode\Errors;
use AP\Validator\US\AddressSanitizes;
use PHPUnit\Framework\TestCase;

final class AddressSanitizesTest extends TestCase
{

    public function good(string $expected, string $actual)
    {
        // actual can be changed
        $validationResult = (new AddressSanitizes())->validate($actual);
        $this->assertTrue($validationResult);
        $this->assertEquals($expected, $actual);
    }

    public function error(string $actual)
    {
        $validationResult = (new AddressSanitizes)->validate($actual);
        $this->assertInstanceOf(Errors::class, $validationResult);
    }

    public function testNameSanitization()
    {
        // Basic valid addresses should remain unchanged
        $this->good("123 Main St", "123 Main St");
        $this->good("400 Elm St. #10B", "400 Elm St. #10B");
        $this->good("St. John's Ave", "St. John's Ave.");
        $this->good("5th & Main St", "5th & Main St.");
        $this->good("Apt. 4B, 500 Elm St", "Apt. 4B, 500 Elm St.");
        $this->good("Tower A (North Side)", "Tower A (North Side)");
        $this->good("Military Base: Sector 5", "Military Base: Sector 5");

        // Extra spaces should be collapsed
        $this->good("123 Main St", "  123   Main   St  ");
        $this->good("Apt. 4B, 500 Elm St", "Apt.   4B,   500  Elm   St.");

        // Multiple dashes should be reduced to one
        $this->good("55-Sunset Blvd", "55--Sunset Blvd");

        // Multiple apostrophes should be reduced to one
        $this->good("O'Brien St", "O''Brien St");
        $this->good("St. John's Ave", "St. John''s Ave.");

        // Multiple dots should be reduced to one
        $this->good("St. Louis Rd", "St.. Louis Rd.");
        $this->good("Apt. 5B, 123 Main St", "Apt.... 5B, 123 Main St.");

        // Multiple commas should be reduced to one
        $this->good("Apt. 4B, 500 Elm St", "Apt. 4B,, 500 Elm St.");
        $this->good("Unit #12, 10 Broadway", "Unit #12,,, 10 Broadway");

        // Multiple slashes should be reduced to one
        $this->good("25/27 Maple Ave", "25//27 Maple Ave");

        // Ensure special characters attach correctly
        $this->good("Tower A (North Side)", "Tower A ( North Side )");
        $this->good("Military Base: Sector 5", "Military Base : Sector 5");

        // Trim leading/trailing spaces and special characters
        $this->good("123 Main St", "   123 Main St   ");
        $this->good("123 Main St", "---123 Main St---");
        $this->good("400 Elm St. #10B", "'400 Elm St. #10B'");
        $this->good("St. John's Ave", "-St. John's Ave.-");
    }
}
