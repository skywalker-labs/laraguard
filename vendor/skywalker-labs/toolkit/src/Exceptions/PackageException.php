<?php


namespace Skywalker\Support\Exceptions;

use Exception;

/**
 * Class     PackageException
 *
 * @author   Skywalker <skywalker@example.com>
 */
class PackageException extends Exception
{
    public static function unspecifiedName(): self
    {
        return new static('You must specify the vendor/package name.');
    }
}
