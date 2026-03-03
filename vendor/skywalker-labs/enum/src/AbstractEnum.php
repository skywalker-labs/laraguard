<?php

declare(strict_types=1);

namespace Skywalker\Enum;

use Skywalker\Enum\Exception\CloneNotSupportedException;
use Skywalker\Enum\Exception\IllegalArgumentException;
use Skywalker\Enum\Exception\MismatchException;
use Skywalker\Enum\Exception\SerializeNotSupportedException;
use Skywalker\Enum\Exception\UnserializeNotSupportedException;
use JsonSerializable;
use ReflectionClass;

/**
 * Base class for all enum implementations.
 *
 * This class provides the foundational logic for creating type-safe, singleton-based
 * enums in PHP. Subclasses should define their enum constants as `protected const`.
 */
abstract class AbstractEnum implements JsonSerializable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $ordinal;

    /**
     * @var array<string, array<string, static>>
     */
    private static $values = [];

    /**
     * @var array<string, bool>
     */
    private static $allValuesLoaded = [];

    /**
     * @var array<string, array>
     */
    private static $constants = [];

    /**
     * @var string
     */
    public static $jsonMode = 'name'; // 'name', 'value', or 'object'

    /**
     * The constructor is private by default to avoid arbitrary enum creation.
     *
     * When creating your own constructor for a parameterized enum, make sure to declare it as protected, so that
     * the static methods are able to construct it. Avoid making it public, as that would allow creation of
     * non-singleton enum instances.
     */
    private function __construct() {}

    /**
     * Magic getter which forwards all calls to {@see self::valueOf()}.
     *
     * @return static
     */
    final public static function __callStatic(string $name, array $arguments): self
    {
        return static::valueOf($name);
    }

    /**
     * Returns an enum with the specified name.
     *
     * The name must match exactly an identifier used to declare an enum in this type (extraneous whitespace characters
     * are not permitted).
     *
     * @return static
     * @throws IllegalArgumentException if the enum has no constant with the specified name
     */
    final public static function valueOf(string $name): self
    {
        if (isset(self::$values[static::class][$name])) {
            return self::$values[static::class][$name];
        }

        $constants = self::constants();

        if (array_key_exists($name, $constants)) {
            return self::createValue($name, $constants[$name][0], $constants[$name][1]);
        }

        throw new IllegalArgumentException(sprintf('No enum constant %s::%s', static::class, $name));
    }

    /**
     * @return static
     */
    private static function createValue(string $name, int $ordinal, $value): self
    {
        $arguments = is_array($value) ? $value : ($value === null ? [] : [$value]);
        $instance = new static(...$arguments);
        $instance->name = $name;
        $instance->ordinal = $ordinal;
        self::$values[static::class][$name] = $instance;
        return $instance;
    }

    /**
     * Obtains all possible types defined by this enum.
     *
     * @return static[]
     */
    final public static function values(): array
    {
        if (isset(self::$allValuesLoaded[static::class])) {
            return array_values(self::$values[static::class]);
        }

        if (! isset(self::$values[static::class])) {
            self::$values[static::class] = [];
        }

        foreach (self::constants() as $name => $constant) {
            if (array_key_exists($name, self::$values[static::class])) {
                continue;
            }

            static::createValue($name, $constant[0], $constant[1]);
        }

        uasort(self::$values[static::class], function (self $a, self $b) {
            return $a->ordinal() <=> $b->ordinal();
        });

        self::$allValuesLoaded[static::class] = true;
        return array_values(self::$values[static::class]);
    }

    /**
     * Returns an enum with the specified name (case-insensitive).
     *
     * @param string $name
     * @return static
     * @throws IllegalArgumentException
     */
    final public static function fromNameInsensitive(string $name): self
    {
        $enum = static::tryFromNameInsensitive($name);
        if (null === $enum) {
            throw new IllegalArgumentException(sprintf('No enum constant %s matching name %s (insensitive)', static::class, $name));
        }
        return $enum;
    }

    /**
     * Returns an enum with the specified name (case-insensitive), or null if not found.
     *
     * @param string $name
     * @return static|null
     */
    final public static function tryFromNameInsensitive(string $name): ?self
    {
        $lowerName = strtolower($name);
        foreach (static::values() as $enum) {
            if (strtolower($enum->name()) === $lowerName) {
                return $enum;
            }
        }
        return null;
    }

    /**
     * Returns an enum with the specified value.
     * Alias for fromValue().
     *
     * @param mixed $value
     * @return static
     */
    final public static function from($value): self
    {
        return static::fromValue($value);
    }

    /**
     * Returns an enum with the specified value, or null if it doesn't exist.
     * Alias for tryFromValue().
     *
     * @param mixed $value
     * @return static|null
     */
    final public static function tryFrom($value): ?self
    {
        return static::tryFromValue($value);
    }

    /**
     * Returns an enum with the specified name.
     * Alias for valueOf().
     *
     * @param string $name
     * @return static
     */
    final public static function fromName(string $name): self
    {
        return static::valueOf($name);
    }

    /**
     * Returns an enum with the specified name, or null if it doesn't exist.
     *
     * @param string $name
     * @return static|null
     */
    final public static function tryFromName(string $name): ?self
    {
        try {
            return static::valueOf($name);
        } catch (IllegalArgumentException $e) {
            return null;
        }
    }

    /**
     * Returns an enum with the specified value.
     *
     * @param mixed $value
     * @return static
     * @throws IllegalArgumentException if no enum constant with the specified value exists
     */
    final public static function fromValue($value): self
    {
        $enum = static::tryFromValue($value);

        if (null === $enum) {
            throw new IllegalArgumentException(sprintf(
                'No enum constant in %s with value %s',
                static::class,
                is_scalar($value) ? (string) $value : gettype($value)
            ));
        }

        return $enum;
    }

    /**
     * Returns an enum with the specified value, or null if it doesn't exist.
     *
     * @param mixed $value
     * @return static|null
     */
    final public static function tryFromValue($value): ?self
    {
        foreach (static::values() as $enum) {
            if ($enum->value() === $value) {
                return $enum;
            }
        }

        return null;
    }

    /**
     * Checks if a name is valid for this enum.
     */
    final public static function isValidName(string $name): bool
    {
        return array_key_exists($name, static::constants());
    }

    /**
     * Checks if a value is valid for this enum.
     */
    final public static function isValidValue($value): bool
    {
        return null !== static::tryFromValue($value);
    }

    /**
     * Returns a random enum instance.
     *
     * @return static
     */
    final public static function random(): self
    {
        $values = static::values();
        return $values[array_rand($values)];
    }

    /**
     * Returns an associative array of the enum [name => label].
     *
     * @return array<string, string>
     */
    final public static function toArray(): array
    {
        $array = [];
        foreach (static::values() as $enum) {
            $array[$enum->name()] = $enum->label();
        }
        return $array;
    }

    private static function constants(): array
    {
        if (isset(self::$constants[static::class])) {
            return self::$constants[static::class];
        }

        self::$constants[static::class] = [];
        $reflectionClass = new ReflectionClass(static::class);
        $ordinal = -1;

        foreach ($reflectionClass->getReflectionConstants() as $reflectionConstant) {
            if (! $reflectionConstant->isProtected()) {
                continue;
            }

            $value = $reflectionConstant->getValue();

            self::$constants[static::class][$reflectionConstant->name] = [
                ++$ordinal,
                $value
            ];
        }

        return self::$constants[static::class];
    }

    /**
     * Returns the name of this enum constant, exactly as declared in its enum declaration.
     *
     * @return string
     */
    final public function name(): string
    {
        return $this->name;
    }

    /**
     * Returns the ordinal of this enumeration constant.
     *
     * The ordinal is the position in its enum declaration, where the initial
     * constant is assigned an ordinal of zero.
     *
     * @return int
     */
    final public function ordinal(): int
    {
        return $this->ordinal;
    }

    /**
     * Compares this enum with the specified object for order.
     *
     * Returns negative integer, zero or positive integer as this object is less than, equal to or greater than the
     * specified object.
     *
     * Enums are only comparable to other enums of the same type. The natural order implemented by this method is the
     * order in which the constants are declared.
     *
     * @throws MismatchException if the passed enum is not of the same type
     */
    final public function compareTo(self $other): int
    {
        if (! $other instanceof static) {
            throw new MismatchException(sprintf(
                'The passed enum %s is not of the same type as %s',
                get_class($other),
                static::class
            ));
        }

        return $this->ordinal - $other->ordinal;
    }

    /**
     * Returns true if this enum appears before the other enum in declaration order.
     */
    final public function isBefore(self $other): bool
    {
        return $this->compareTo($other) < 0;
    }

    /**
     * Returns true if this enum appears after the other enum in declaration order.
     */
    final public function isAfter(self $other): bool
    {
        return $this->compareTo($other) > 0;
    }

    /**
     * Returns the next enum in declaration order, or null if this is the last one.
     *
     * @return static|null
     */
    final public function next(): ?self
    {
        $values = static::values();
        return $values[$this->ordinal + 1] ?? null;
    }

    /**
     * Returns the previous enum in declaration order, or null if this is the first one.
     *
     * @return static|null
     */
    final public function previous(): ?self
    {
        $values = static::values();
        return $values[$this->ordinal - 1] ?? null;
    }

    /**
     * Forbid cloning enums.
     *
     * @throws CloneNotSupportedException
     */
    final public function __clone()
    {
        throw new CloneNotSupportedException();
    }

    /**
     * Forbid serializing enums.
     *
     * @throws SerializeNotSupportedException
     */
    final public function __sleep(): array
    {
        throw new SerializeNotSupportedException();
    }

    /**
     * Forbid serializing enums.
     *
     * @throws SerializeNotSupportedException
     */
    final public function __serialize(): array
    {
        throw new SerializeNotSupportedException();
    }

    /**
     * Forbid unserializing enums.
     *
     * @throws UnserializeNotSupportedException
     */
    final public function __wakeup(): void
    {
        throw new UnserializeNotSupportedException();
    }

    /**
     * Forbid unserializing enums.
     *
     * @throws UnserializeNotSupportedException
     */
    final public function __unserialize($arg): void
    {
        throw new UnserializeNotSupportedException();
    }

    /**
     * Turns the enum into a string representation.
     *
     * You may override this method to give a more user-friendly version.
     */
    public function __toString(): string
    {
        return $this->label();
    }

    /**
     * Returns the raw value of the enum constant.
     *
     * @return mixed
     */
    final public function value()
    {
        $constants = self::constants();
        return $constants[$this->name][1];
    }

    /**
     * Returns the name of the constant in lowercase.
     */
    public function lowerName(): string
    {
        return strtolower($this->name);
    }

    /**
     * Returns the name of the constant in CamelCase (UpperCamelCase).
     */
    public function camelName(): string
    {
        return lcfirst(str_replace(' ', '', ucwords(strtolower(str_replace('_', ' ', $this->name)))));
    }

    /**
     * Returns a human-readable label for the enum constant.
     *
     * By default, this returns the name of the constant. Override this method
     * in your subclass to provide custom labels.
     */
    public function label(): string
    {
        return $this->name;
    }

    /**
     * Checks if this enum is any of the specified enums.
     *
     * @param array<self> $enums
     */
    final public function isAnyOf(array $enums): bool
    {
        return in_array($this, $enums, true);
    }

    /**
     * Checks if this enum is none of the specified enums.
     *
     * @param array<self> $enums
     */
    final public function isNoneOf(array $enums): bool
    {
        return !$this->isAnyOf($enums);
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        switch (static::$jsonMode) {
            case 'value':
                return $this->value();
            case 'object':
                return [
                    'name' => $this->name,
                    'value' => $this->value(),
                    'label' => $this->label(),
                    'ordinal' => $this->ordinal,
                ];
            default:
                return $this->name;
        }
    }
}
