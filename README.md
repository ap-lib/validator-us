# AP\Validator\US

[![MIT License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A set of US-specific input validators, ensuring proper formatting and sanitization.

## Installation

```bash
composer require ap-lib/validator-us
```

## Modules

- **CitySanitizes** - Ensures valid US city names, removing invalid characters and normalizing formatting.
- **NameSanitizes** - Sanitizes personal names, ensuring proper structure while preserving necessary characters.

## Requirements

- PHP 8.3 or higher

## Getting started

### Sanitizing a City Name
```php
use AP\Validator\US\CitySanitizes;

$city = "  New!! York--";
$validator = new CitySanitizes();

if ($validator->validateString($city) === true) {
    echo "Sanitized City: " . $city; // Output: "New York"
} else {
    echo "Invalid city name!";
}
```

### Sanitizing a Personal Name
```php
use AP\Validator\US\NameSanitizes;

$name = "  O``Brien--";
$validator = new NameSanitizes();

if ($validator->validateString($name) === true) {
    echo "Sanitized Name: " . $name; // Output: "O'Brien"
} else {
    echo "Invalid name!";
}
```