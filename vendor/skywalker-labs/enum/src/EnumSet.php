<?php

declare(strict_types=1);

namespace Skywalker\Enum;

use Skywalker\Enum\Exception\IllegalArgumentException;
use IteratorAggregate;
use Countable;
use Traversable;

/**
 * A specialized set implementation for use with enum types.
 *
 * All of the elements in an enum set must come from a single enum type that is
 * specified, when the set is created. Enum sets are maintained in the natural
 * order of their elements (the order in which the enum constants are declared).
 */
final class EnumSet implements IteratorAggregate, Countable
{
    /**
     * @var string
     */
    private $enumType;

    /**
     * @var array<int, AbstractEnum>
     */
    private $elements = [];

    /**
     * Creates a new enum set.
     *
     * @param string $enumType the type of the elements, must extend AbstractEnum
     * @throws IllegalArgumentException when enum type does not extend AbstractEnum
     */
    public function __construct(string $enumType)
    {
        if (!is_subclass_of($enumType, AbstractEnum::class)) {
            throw new IllegalArgumentException(sprintf(
                'Class %s does not extend %s',
                $enumType,
                AbstractEnum::class
            ));
        }
        $this->enumType = $enumType;
    }

    /**
     * Create an enum set containing the specified elements.
     *
     * @param string $enumType
     * @param AbstractEnum ...$elements
     * @return self
     */
    public static function of(string $enumType, AbstractEnum ...$elements): self
    {
        $set = new self($enumType);
        foreach ($elements as $element) {
            $set->add($element);
        }
        return $set;
    }

    /**
     * Create an enum set containing all elements of the specified type.
     *
     * @param string $enumType
     * @return self
     */
    public static function allOf(string $enumType): self
    {
        $set = new self($enumType);
        foreach ($enumType::values() as $element) {
            $set->add($element);
        }
        return $set;
    }

    /**
     * Create an enum set from a bitmask.
     *
     * @param string $enumType
     * @param int $bitmask
     * @return self
     */
    public static function fromBitmask(string $enumType, int $bitmask): self
    {
        $set = new self($enumType);
        foreach ($enumType::values() as $element) {
            if (($bitmask & (1 << $element->ordinal())) !== 0) {
                $set->add($element);
            }
        }
        return $set;
    }

    /**
     * Create an enum set containing a range of elements.
     *
     * @param AbstractEnum $from
     * @param AbstractEnum $to
     * @return self
     * @throws IllegalArgumentException if enums are of different types
     */
    public static function range(AbstractEnum $from, AbstractEnum $to): self
    {
        if (get_class($from) !== get_class($to)) {
            throw new IllegalArgumentException('Range enums must be of the same type');
        }

        $enumType = get_class($from);
        $set = new self($enumType);
        $values = $enumType::values();

        $start = min($from->ordinal(), $to->ordinal());
        $end = max($from->ordinal(), $to->ordinal());

        for ($i = $start; $i <= $end; $i++) {
            $set->add($values[$i]);
        }

        return $set;
    }

    /**
     * Adds the specified element to this set if it is not already present.
     *
     * @param AbstractEnum $element
     * @return bool true if this set did not already contain the specified element
     */
    public function add(AbstractEnum $element): bool
    {
        $this->checkType($element);
        $ordinal = $element->ordinal();
        if (isset($this->elements[$ordinal])) {
            return false;
        }
        $this->elements[$ordinal] = $element;
        ksort($this->elements);
        return true;
    }

    /**
     * Removes the specified element from this set if it is present.
     *
     * @param AbstractEnum $element
     * @return bool true if the set contained the specified element
     */
    public function remove(AbstractEnum $element): bool
    {
        $this->checkType($element);
        $ordinal = $element->ordinal();
        if (!isset($this->elements[$ordinal])) {
            return false;
        }
        unset($this->elements[$ordinal]);
        return true;
    }

    /**
     * Returns true if this set contains the specified element.
     *
     * @param AbstractEnum $element
     * @return bool
     */
    public function contains(AbstractEnum $element): bool
    {
        $this->checkType($element);
        return isset($this->elements[$element->ordinal()]);
    }

    /**
     * Returns the number of elements in this set.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * Returns the bitmask representation of this set.
     *
     * @return int
     */
    public function toBitmask(): int
    {
        $bitmask = 0;
        foreach ($this->elements as $element) {
            $bitmask |= (1 << $element->ordinal());
        }
        return $bitmask;
    }

    /**
     * Filters elements using a predicate.
     *
     * @param callable $p
     * @return self
     */
    public function filter(callable $p): self
    {
        $newSet = new self($this->enumType);
        foreach ($this->elements as $element) {
            if ($p($element)) {
                $newSet->add($element);
            }
        }
        return $newSet;
    }

    /**
     * Transforms elements using a mapper.
     *
     * @param callable $f
     * @return array
     */
    public function map(callable $f): array
    {
        return array_map($f, array_values($this->elements));
    }

    /**
     * Returns the first element in the set, or null if empty.
     *
     * @return AbstractEnum|null
     */
    public function first(): ?AbstractEnum
    {
        if (empty($this->elements)) {
            return null;
        }
        return reset($this->elements);
    }

    /**
     * Returns the last element in the set, or null if empty.
     *
     * @return AbstractEnum|null
     */
    public function last(): ?AbstractEnum
    {
        if (empty($this->elements)) {
            return null;
        }
        return end($this->elements);
    }

    /**
     * Checks if any element matches the predicate.
     *
     * @param callable $p
     * @return bool
     */
    public function any(callable $p): bool
    {
        foreach ($this->elements as $element) {
            if ($p($element)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if all elements match the predicate.
     *
     * @param callable $p
     * @return bool
     */
    public function all(callable $p): bool
    {
        foreach ($this->elements as $element) {
            if (!$p($element)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if no elements match the predicate.
     *
     * @param callable $p
     * @return bool
     */
    public function none(callable $p): bool
    {
        return !$this->any($p);
    }

    /**
     * Finds the first element matching the predicate.
     *
     * @param callable $p
     * @return AbstractEnum|null
     */
    public function find(callable $p): ?AbstractEnum
    {
        foreach ($this->elements as $element) {
            if ($p($element)) {
                return $element;
            }
        }
        return null;
    }

    /**
     * Reduces the set to a single value.
     *
     * @param callable $f
     * @param mixed $initial
     * @return mixed
     */
    public function reduce(callable $f, $initial = null)
    {
        return array_reduce(array_values($this->elements), $f, $initial);
    }

    /**
     * Returns a new set that is the union of this set and another set.
     */
    public function union(self $other): self
    {
        if ($this->enumType !== $other->enumType) {
            throw new IllegalArgumentException('Sets must be of the same enum type');
        }

        $newSet = clone $this;
        foreach ($other->elements as $element) {
            $newSet->add($element);
        }
        return $newSet;
    }

    /**
     * Returns a new set that is the intersection of this set and another set.
     */
    public function intersect(self $other): self
    {
        if ($this->enumType !== $other->enumType) {
            throw new IllegalArgumentException('Sets must be of the same enum type');
        }

        $newSet = new self($this->enumType);
        foreach ($this->elements as $element) {
            if ($other->contains($element)) {
                $newSet->add($element);
            }
        }
        return $newSet;
    }

    /**
     * Returns a new set that is the difference of this set and another set.
     */
    public function diff(self $other): self
    {
        if ($this->enumType !== $other->enumType) {
            throw new IllegalArgumentException('Sets must be of the same enum type');
        }

        $newSet = new self($this->enumType);
        foreach ($this->elements as $element) {
            if (!$other->contains($element)) {
                $newSet->add($element);
            }
        }
        return $newSet;
    }

    /**
     * Returns a new set that is the complement of this set.
     */
    public function complement(): self
    {
        $newSet = new self($this->enumType);
        foreach ($this->enumType::values() as $element) {
            if (!$this->contains($element)) {
                $newSet->add($element);
            }
        }
        return $newSet;
    }

    /**
     * Returns an iterator over the elements in this set.
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        foreach ($this->elements as $element) {
            yield $element;
        }
    }

    /**
     * Checks if the element is of the correct enum type.
     *
     * @param AbstractEnum $element
     * @throws IllegalArgumentException
     */
    private function checkType(AbstractEnum $element): void
    {
        if (!$element instanceof $this->enumType) {
            throw new IllegalArgumentException(sprintf(
                'Element of type %s is not compatible with %s',
                get_class($element),
                $this->enumType
            ));
        }
    }
}
