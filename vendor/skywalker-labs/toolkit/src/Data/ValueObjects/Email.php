<?php

namespace Skywalker\Support\Data\ValueObjects;

use Skywalker\Support\Data\ValueObject;
use InvalidArgumentException;

class Email extends ValueObject
{
    /**
     * The email address.
     *
     * @var string
     */
    protected $email;

    /**
     * Create a new Email instance.
     *
     * @param  string  $email
     * @throws \InvalidArgumentException
     */
    public function __construct(string $email)
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email address: {$email}");
        }

        $this->email = $email;
    }

    /**
     * Return string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->email;
    }
}
