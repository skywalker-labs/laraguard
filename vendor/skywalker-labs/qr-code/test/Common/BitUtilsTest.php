<?php
declare(strict_types = 1);

namespace Skywalker\QrCodeTest\Common;

use Skywalker\QrCode\Common\BitUtils;
use PHPUnit\Framework\TestCase;

class BitUtilsTest extends TestCase
{
    public function testUnsignedRightShift() : void
    {
        $this->assertSame(1, BitUtils::unsignedRightShift(1, 0));
        $this->assertSame(1, BitUtils::unsignedRightShift(10, 3));
        $this->assertSame(536870910, BitUtils::unsignedRightShift(-10, 3));
    }

    public function testNumberOfTrailingZeros() : void
    {
        $this->assertSame(32, BitUtils::numberOfTrailingZeros(0));
        $this->assertSame(1, BitUtils::numberOfTrailingZeros(10));
        $this->assertSame(0, BitUtils::numberOfTrailingZeros(15));
        $this->assertSame(2, BitUtils::numberOfTrailingZeros(20));
    }
}

