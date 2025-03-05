<?php declare(strict_types=1);

namespace AP\Validator\US\Tests;

use AP\ErrorNode\Errors;
use AP\Validator\US\CitySanitizes;
use AP\Validator\US\NameSanitizes;
use PHPUnit\Framework\TestCase;

final class NameSanitizesTest extends TestCase
{

    public function good(string $expected, string $actual)
    {
        // actual can be changed
        $validationResult = (new NameSanitizes)->validate($actual);
        $this->assertTrue($validationResult);
        $this->assertEquals($expected, $actual);
    }

    public function error(string $actual)
    {
        $validationResult = (new NameSanitizes)->validate($actual);
        $this->assertInstanceOf(Errors::class, $validationResult);
    }

    public function testNameSanitization()
    {
        // Basic valid names should remain unchanged
        $this->good("John", "John");
        $this->good("Mary Ann", "Mary Ann");
        $this->good("Jean-Pierre", "Jean-Pierre");
        $this->good("O'Brien", "O'Brien");

        // Extra spaces should be collapsed
        $this->good("John Doe", "John   Doe");
        $this->good("Mary Ann", "  Mary    Ann  ");

        // Non-Latin characters should be removed
        $this->good("John", "John!!!");
        $this->good("John", "John@#%^");
        $this->good("O'Brien", "O``Brien");

        // Multiple dashes should be reduced to one
        $this->good("Jean-Pierre", "Jean--Pierre");
        $this->good("Jean-Pierre", "Jean---Pierre");

        // Multiple apostrophes should be reduced to one
        $this->good("O'Brien", "O''Brien");
        $this->good("O'Brien", "O'''Brien");

        // Remove repeated apostrophe/dash combinations
        $this->good("Jean-Pierre", "Jean--```-Pierre");

        // Ensure dashes and apostrophes attach to words correctly
        $this->good("De'Aundra", "De' Aundra");
        $this->good("Jean-Pierre", "Jean - Pierre");
        $this->good("Jean-Pierre", "Jean  -  Pierre");

        // Trim leading and trailing spaces, dashes, and apostrophes
        $this->good("John", "   John   ");
        $this->good("John", "---John---");
        $this->good("O'Brien", "'O'Brien'");
        $this->good("Jean-Pierre", "-Jean-Pierre-");

        // Names should not exceed 32 characters
        // Trim leading/trailing spaces, dashes, and apostrophes after truncating to 32 characters.
        // The 32-character limit is set because real-world name entries typically don't exceed this length.
        $this->good("Alexandrianna-Joanne McCallister", "Alexandrianna-Joanne McCallister");
        $this->good("Alexandrianna-Joanne McCallister", "Alexandrianna-Joanne McCallister the Third");

        // Non-Latin names should be rejected
        $this->error("Александр");
        $this->error("山田太郎");
        $this->error("محمد");
    }
}
