<?php

namespace Skywalker\Support\Data;

use JsonSerializable;

abstract class ValueObject implements JsonSerializable
{
    /**
     * Check equality with another Value Object.
     *
     * @param  \Skywalker\Support\Data\ValueObject  $object
     * @return bool
     */
    public function equals(ValueObject $object): bool
    {
        if (get_class($this) !== get_class($object)) {
            return false;
        }

        return $this->__toString() === $object->__toString();
    }

    /**
     * Return string representation.
     *
     * @return string
     */
    abstract public function __toString(): string;

    /**
     * Serialize to JSON.
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->__toString();
    }
}
