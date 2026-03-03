# Skywalker Enum

A robust, type-safe, and singleton-based Enum implementation for PHP.

This library provides a clean way to implement enums in PHP, ensuring that each enum value is a unique singleton instance. It works across PHP versions from 7.0 to 9.0+.

## Installation

Install the package via Composer:

```bash
composer require skywalker-labs/enum
```

## Basic Usage

To create an enum, extend `Skywalker\Enum\AbstractEnum` and define your constants as `protected const`.

```php
use Skywalker\Enum\AbstractEnum;

/**
 * @method static self MONDAY()
 * @method static self TUESDAY()
 * ...
 */
final class WeekDay extends AbstractEnum
{
    protected const MONDAY = null;
    protected const TUESDAY = null;
    // ...
}
```

## Pro Features

### 1. Flexible JSON Serialization

Control how your enums appear in API responses.

```php
// Choose between 'name', 'value', or 'object'
AbstractEnum::$jsonMode = 'object';

echo json_encode(WeekDay::MONDAY());
// Output: {"name":"MONDAY","value":null,"label":"MONDAY","ordinal":0}
```

### 2. Native PHP Compatibility

Use aliases that match PHP 8.1+ native enums for a smoother transition.

```php
$day = WeekDay::from('MONDAY');     // Throws exception if not found
$day = WeekDay::tryFrom('INVALID'); // Returns null if not found
```

### 3. Randomization

Perfect for seeders and unit tests.

```php
$randomDay = WeekDay::random();
```

### 4. String Helpers

```php
echo WeekDay::MONDAY()->lowerName(); // "monday"
echo WeekDay::MONDAY()->camelName(); // "Monday"
```

### 5. Collection Power-Ups (`EnumSet`)

Functional methods for modern collections.

```php
$set = EnumSet::allOf(WeekDay::class);

$weekends = $set->filter(fn($d) => $d->isAnyOf([WeekDay::SATURDAY(), WeekDay::SUNDAY()]));
$names = $set->map(fn($d) => $d->name());

$first = $set->first(); // MONDAY
$last = $set->last();   // SUNDAY
```

## Ultimate Edition Features

### 1. Navigational Helpers

Sequence through your enums easily.

```php
$day = WeekDay::MONDAY();
$next = $day->next();         // TUESDAY
$prev = $day->previous();     // null

if ($day->isBefore(WeekDay::FRIDAY())) {
    echo "Hang in there!";
}
```

### 2. Set Theory (`EnumSet`)

Perform mathematical set operations.

```php
$workDays = EnumSet::range(WeekDay::MONDAY(), WeekDay::FRIDAY());
$holidays = EnumSet::of(WeekDay::class, WeekDay::MONDAY());

$actualWork = $workDays->diff($holidays);
$allDays = $workDays->union($holidays);
$intersect = $workDays->intersect($holidays);
$restOfYear = $workDays->complement();
```

### 3. Tolerant Lookups

Handle user input with case-insensitivity.

```php
$day = WeekDay::fromNameInsensitive('monday'); // Returns MONDAY instance
```

## Advanced Features

### Collection & UI Helpers

```php
// Returns ['MONDAY' => 'MONDAY', 'TUESDAY' => 'TUESDAY', ...]
$options = WeekDay::toArray();
```

### Type-Safe Comparisons

```php
if ($day->isAnyOf([WeekDay::SATURDAY(), WeekDay::SUNDAY()])) {
    echo "It's the weekend!";
}
```

### Bitmask Support

Efficiently store multiple enum values in a single integer.

```php
$mask = $set->toBitmask(); // integer e.g. 5
$restored = EnumSet::fromBitmask(WeekDay::class, $mask);
```

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
