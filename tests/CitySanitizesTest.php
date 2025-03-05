<?php declare(strict_types=1);

namespace AP\Validator\US\Tests;

use AP\ErrorNode\Errors;
use AP\Validator\US\CitySanitizes;
use PHPUnit\Framework\TestCase;

final class CitySanitizesTest extends TestCase
{

    public function good(string $expected, string $actual)
    {
        // actual can be changed
        $validationResult = (new CitySanitizes)->validate($actual);
        $this->assertTrue($validationResult);
        $this->assertEquals($expected, $actual);
    }

    public function error(string $actual)
    {
        $validationResult = (new CitySanitizes)->validate($actual);
        $this->assertInstanceOf(Errors::class, $validationResult);
    }

    public function testCitySanitization()
    {
        // Basic valid names should remain unchanged
        $this->good("New York", "New York");
        $this->good("San Francisco", "San Francisco");

        // Extra spaces should be collapsed
        $this->good("New York", "New   York");
        $this->good("New York", "  New   York  ");
        $this->good("San Francisco", "  San    Francisco  ");

        // Non-Latin characters should be removed
        $this->good("New York", "New York!!!");
        $this->good("New York", "New York.");
        $this->good("New York", "New York,");
        $this->good("New York", "New York@#%^");

        // Multiple dashes should be reduced to one
        $this->good("Winston-Salem", "Winston--Salem");
        $this->good("Winston-Salem", "Winston---Salem");

        // Multiple apostrophes should be reduced to one
        $this->good("O'Brien", "O''Brien");
        $this->good("O'Brien", "O'''Brien");
        $this->good("O'Brien", "O``Brien");
        $this->good("O'Brien", "O```Brien");

        // Multiple dots should be reduced to one
        $this->good("St. Louis", "St.. Louis");
        $this->good("St. Louis", "St.... Louis");

        // Multiple commas should be reduced to one
        $this->good("Helena, West Helena", "Helena,, West Helena");
        $this->good("Helena, West Helena", "Helena,,, West Helena");

        // Remove repeated apostrophe/dash/dot/comma combinations
        $this->good("Jean-Pierre", "Jean---Pierre");
        $this->good("O'Brien", "O'''''Brien");
        $this->good("O'Brien", "O``''''Brien");
        $this->good("St. Louis", "St..,, Louis");

        // Ensure dashes and apostrophes attach to words correctly
        $this->good("De'Andra", "De' Andra");
        $this->good("Jean-Pierre", "Jean - Pierre");
        $this->good("Jean-Pierre", "Jean  -  Pierre");

        // Trim leading and trailing spaces, dashes, apostrophes, dots, and commas
        $this->good("New York", "   New York   ");
        $this->good("New York", "---New York---");
        $this->good("New York", "'New York'");
        $this->good("New York", ",New York,");
        $this->good("New York", ".New York.");

        // Names should not exceed 32 characters
        // Trim leading/trailing spaces, dashes, apostrophes, dots, and commas after truncating to 32 characters.
        // The 32-character limit is set based on real-world user input data, where the longest known U.S. city name
        // is "City of the Village of Clarkston"
        $this->good("City of the Village of Clarkston", "City of the Village of Clarkston");
        $this->good("City of the Village of Clarkston", "City of the Village of Clarkston i am live");

        // No Latin
        $this->error("Нью-Йорк");
        $this->error("ニューヨーク");
    }
}
