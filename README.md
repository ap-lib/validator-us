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

### Sanitizing a City
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

### Sanitizing a Name
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

### Sanitizing an Address
```php
use AP\Validator\US\AddressSanitizes;

$address = "  400 Elm St. #10B  ";
$validator = new AddressSanitizes();

if ($validator->validate($address) === true) {
    echo "Sanitized Address: " . $address; // Output: "400 Elm St. #10B"
} else {
    echo "Invalid address!";
}
```


### Validating a Phone Number
```php
use AP\Validator\US\PhoneSanitizer;


$validator = new PhoneSanitizer();

# just sanitize convert to int if possible
$phone = "12 - 34";
if ($validator->sanitize($phone) === true) {
    echo "Sanitized Phone: " . $phone; // Output: 1234
} else {
    echo "Invalid phone number!";
}

// sanitize + validation
$phone = "(223) 456-7890";
if ($validator->validate($phone) === true) {
    echo "Sanitized and Valid Phone: " . $phone; // Output: 2234567890
} else {
    echo "Invalid phone number!";
}
```




### Validating an SSN
```php
use AP\Validator\US\SSNSanitizer;

$validator = new SSNSanitizer();

# just sanitize convert to int if possible
$ssn = "1234";
if ($validator->sanitize($ssn) === true) {
    echo "Sanitized SSN: " . $ssn; // Output: 1234
} else {
    echo "Invalid SSN!";
}

// sanitize + validation
$ssn = "123-45-6789";
if ($validator->validate($ssn) === true) {
    echo "Sanitized and Valid SSN: " . $ssn; // Output: 123456789
} else {
    echo "Invalid SSN!";
}
```