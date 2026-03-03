<?php

declare(strict_types=1);

namespace Skywalker\EnumTest;

use Skywalker\Enum\Exception\CloneNotSupportedException;
use Skywalker\Enum\Exception\SerializeNotSupportedException;
use Skywalker\Enum\Exception\UnserializeNotSupportedException;
use Skywalker\Enum\NullValue;
use PHPUnit\Framework\TestCase;

final class NullValueTest extends TestCase
{
    public function testExceptionOnCloneAttempt(): void
    {
        $this->expectException(CloneNotSupportedException::class);
        clone NullValue::instance();
    }

    public function testExceptionOnSerializeAttempt(): void
    {
        $this->expectException(SerializeNotSupportedException::class);
        serialize(NullValue::instance());
    }

    public function testExceptionOnUnserializeAttempt(): void
    {
        $this->expectException(UnserializeNotSupportedException::class);
        unserialize('O:24:"Skywalker\\Enum\\NullValue":0:{}');
    }
}
