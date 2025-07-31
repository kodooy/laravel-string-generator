# String Generator Laravel Package

A Laravel package for generating random strings and collections of unique strings with customizable character sets.

## Features

- Generate single random strings or collections of unique strings
- Configurable character sets (alphanumeric, alphabetic, numeric, etc.)
- Exclude specific characters from generation
- Event system for tracking string generation
- Caching support for performance
- Fluent API with method chaining
- Laravel Facade support
- Comprehensive validation and error handling

## Installation

You can install the package via composer:

```bash
composer require kodooy/laravel-string-generator
```

The package will automatically register itself via Laravel's package discovery.

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=string-generator-config
```

This will create a `config/string-generator.php` file where you can customize:

- Default character sets
- Cache settings
- Event configuration
- Maximum attempt limits

## Usage

### Basic Usage

```php
use Kodooy\StringGenerator\Facades\StringGenerator;

// Generate a single string (default length: 8)
$string = StringGenerator::single();

// Generate a string with custom length
$string = StringGenerator::single(12);

// Generate a collection of unique strings
$strings = StringGenerator::collection(5, 8); // 5 strings, each 8 characters long
```

### Using Different Character Sets

```php
// Alphanumeric (safe characters, excludes similar looking chars)
$string = StringGenerator::alphanumeric()->single(10);

// Alphabetic only
$string = StringGenerator::alpha()->single(10);

// Numeric only
$string = StringGenerator::numeric()->single(6);

// Lowercase only
$string = StringGenerator::lowercase()->single(8);

// Uppercase only
$string = StringGenerator::uppercase()->single(8);

// Custom charset
$string = StringGenerator::charset('xyz123')->single(10);

// Using predefined charset names
$string = StringGenerator::using('alphanumeric_safe')->single(10);
```

### Excluding Characters

```php
// Exclude specific characters
$string = StringGenerator::single(10, 'il1oO0'); // Exclude confusing characters

// With collections
$strings = StringGenerator::collection(5, 8, 'aeiou'); // Exclude vowels
```

### Method Chaining

```php
$string = StringGenerator::alphanumeric()->single(12, 'il1oO0');

$strings = StringGenerator::using('alpha_safe')
    ->collection(10, 6, 'xyz');
```

### Using the Generator Class Directly

```php
use Kodooy\StringGenerator\StringGenerator;

$generator = new StringGenerator();

// Same methods available
$string = $generator->single(8);
$strings = $generator->collection(5, 10);
```

### Universal Generate Method

```php
// Single string (when count is null or 1)
$string = StringGenerator::generate(8);
$string = StringGenerator::generate(8, 1);

// Collection (when count > 1)
$strings = StringGenerator::generate(8, 5); // 5 strings of length 8
```

## Events

The package dispatches events when strings are generated:

- `StringGenerated` - Fired when a single string is generated
- `StringCollectionGenerated` - Fired when a collection is generated

```php
// Listen for events
Event::listen(\Kodooy\StringGenerator\Events\StringGenerated::class, function ($event) {
    // $event->string, $event->length, $event->charset, $event->exclude
    Log::info('String generated: ' . $event->string);
});
```

## Exception Handling

The package throws specific exceptions:

```php
use Kodooy\StringGenerator\Exceptions\InsufficientUniqueStringsException;
use Kodooy\StringGenerator\Exceptions\InvalidCharsetException;

try {
    // This might fail if requesting too many unique strings
    $strings = StringGenerator::charset('ab')->collection(100, 2);
} catch (InsufficientUniqueStringsException $e) {
    // Handle the exception
}
```

## Available Character Sets

The package includes several predefined character sets:

- `alphanumeric` - All letters and numbers
- `alphanumeric_safe` - Letters and numbers, excluding similar looking characters (i, l, I, o, O, 0)
- `alpha` - All letters (upper and lowercase)
- `alpha_safe` - Letters excluding similar looking characters (i, l, I, o, O)
- `numeric` - Numbers 0-9
- `lowercase` - Lowercase letters only
- `uppercase` - Uppercase letters only
- `symbols` - Special symbols

## Testing

Run the tests with:

```bash
composer test
```

Or using Pest directly:

```bash
./vendor/bin/pest
```
