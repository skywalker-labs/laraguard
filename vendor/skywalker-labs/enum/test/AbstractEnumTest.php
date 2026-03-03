<?php

declare(strict_types=1);

namespace Skywalker\EnumTest;

use Skywalker\Enum\AbstractEnum;
use Skywalker\Enum\Exception\CloneNotSupportedException;
use Skywalker\Enum\Exception\IllegalArgumentException;
use Skywalker\Enum\Exception\MismatchException;
use Skywalker\Enum\Exception\SerializeNotSupportedException;
use Skywalker\Enum\Exception\UnserializeNotSupportedException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class AbstractEnumTest extends TestCase
{
    public function setUp(): void
    {
        $reflectionClass = new ReflectionClass(AbstractEnum::class);

        $constantsProperty = $reflectionClass->getProperty('constants');
        $constantsProperty->setAccessible(true);
        $constantsProperty->setValue([]);

        $valuesProperty = $reflectionClass->getProperty('values');
        $valuesProperty->setAccessible(true);
        $valuesProperty->setValue([]);

        $allValuesLoadedProperty = $reflectionClass->getProperty('allValuesLoaded');
        $allValuesLoadedProperty->setAccessible(true);
        $allValuesLoadedProperty->setValue([]);
    }

    public function testToString(): void
    {
        $weekday = WeekDay::FRIDAY();
        self::assertSame('FRIDAY', (string) $weekday);
    }

    public function testName(): void
    {
        $this->assertSame('WEDNESDAY', WeekDay::WEDNESDAY()->name());
    }

    public function testOrdinal(): void
    {
        $this->assertSame(2, WeekDay::WEDNESDAY()->ordinal());
    }

    public function testSameInstanceIsReturned(): void
    {
        self::assertSame(WeekDay::FRIDAY(), WeekDay::FRIDAY());
    }

    public function testValueOf(): void
    {
        self::assertSame(WeekDay::FRIDAY(), WeekDay::valueOf('FRIDAY'));
    }

    public function testValueOfInvalidConstant(): void
    {
        $this->expectException(IllegalArgumentException::class);
        WeekDay::valueOf('CATURDAY');
    }

    public function testExceptionOnCloneAttempt(): void
    {
        $this->expectException(CloneNotSupportedException::class);
        clone WeekDay::FRIDAY();
    }

    public function testExceptionOnSerializeAttempt(): void
    {
        $this->expectException(SerializeNotSupportedException::class);
        serialize(WeekDay::FRIDAY());
    }

    public function testExceptionOnUnserializeAttempt(): void
    {
        $this->expectException(UnserializeNotSupportedException::class);
        unserialize('O:26:"Skywalker\\EnumTest\\WeekDay":0:{}');
    }

    public function testReturnValueOfValuesIsSortedByOrdinal(): void
    {
        // Initialize some week days out of order
        WeekDay::SATURDAY();
        WeekDay::TUESDAY();

        $ordinals = array_values(array_map(function (WeekDay $weekDay): int {
            return $weekDay->ordinal();
        }, WeekDay::values()));

        self::assertSame([0, 1, 2, 3, 4, 5, 6], $ordinals);

        $cachedOrdinals = array_values(array_map(function (WeekDay $weekDay): int {
            return $weekDay->ordinal();
        }, WeekDay::values()));
        $this->assertSame($ordinals, $cachedOrdinals);
    }

    public function testCompareTo(): void
    {
        $this->assertSame(-4, WeekDay::WEDNESDAY()->compareTo(WeekDay::SUNDAY()));
        $this->assertSame(4, WeekDay::SUNDAY()->compareTo(WeekDay::WEDNESDAY()));
        $this->assertSame(0, WeekDay::WEDNESDAY()->compareTo(WeekDay::WEDNESDAY()));
    }

    public function testCompareToWrongEnum(): void
    {
        $this->expectException(MismatchException::class);
        WeekDay::MONDAY()->compareTo(Planet::EARTH());
    }

    public function testParameterizedEnum(): void
    {
        $planet = Planet::EARTH();
        $this->assertSame(5.976e+24, $planet->mass());
        $this->assertSame(6.37814e6, $planet->radius());
    }

    public function testFromValue(): void
    {
        $this->assertSame(Planet::EARTH(), Planet::fromValue([5.976e+24, 6.37814e6]));
    }

    public function testFromValueInvalid(): void
    {
        $this->expectException(IllegalArgumentException::class);
        Planet::fromValue('INVALID');
    }

    public function testTryFromValue(): void
    {
        $this->assertSame(Planet::EARTH(), Planet::tryFromValue([5.976e+24, 6.37814e6]));
        $this->assertNull(Planet::tryFromValue('INVALID'));
    }

    public function testValue(): void
    {
        $this->assertSame([5.976e+24, 6.37814e6], Planet::EARTH()->value());
    }

    public function testLabel(): void
    {
        $this->assertSame('EARTH', Planet::EARTH()->label());
    }

    public function testIsValidName(): void
    {
        $this->assertTrue(Planet::isValidName('EARTH'));
        $this->assertFalse(Planet::isValidName('PLUTO'));
    }

    public function testIsValidValue(): void
    {
        $this->assertTrue(Planet::isValidValue([5.976e+24, 6.37814e6]));
        $this->assertFalse(Planet::isValidValue('INVALID'));
    }

    public function testJsonSerialize(): void
    {
        AbstractEnum::$jsonMode = 'name';
        $this->assertSame('"EARTH"', json_encode(Planet::EARTH()));

        AbstractEnum::$jsonMode = 'value';
        $this->assertSame('[5.976e+24,6378140]', json_encode(Planet::EARTH()));

        AbstractEnum::$jsonMode = 'object';
        $json = json_decode(json_encode(Planet::EARTH()), true);
        $this->assertSame('EARTH', $json['name']);
        $this->assertSame(2, $json['ordinal']);

        // Reset for other tests
        AbstractEnum::$jsonMode = 'name';
    }

    public function testAliases(): void
    {
        $this->assertSame(WeekDay::MONDAY(), WeekDay::from(null));
        $this->assertSame(WeekDay::MONDAY(), WeekDay::tryFrom(null));
        $this->assertSame(WeekDay::MONDAY(), WeekDay::fromName('MONDAY'));
        $this->assertSame(WeekDay::MONDAY(), WeekDay::tryFromName('MONDAY'));
        $this->assertNull(WeekDay::tryFromName('INVALID'));
    }

    public function testInsensitiveLookups(): void
    {
        $this->assertSame(WeekDay::MONDAY(), WeekDay::fromNameInsensitive('monday'));
        $this->assertSame(WeekDay::MONDAY(), WeekDay::fromNameInsensitive('MoNdAy'));
        $this->assertSame(WeekDay::MONDAY(), WeekDay::tryFromNameInsensitive('monday'));
        $this->assertNull(WeekDay::tryFromNameInsensitive('INVALID'));
    }

    public function testNavigation(): void
    {
        $this->assertSame(WeekDay::TUESDAY(), WeekDay::MONDAY()->next());
        $this->assertSame(WeekDay::MONDAY(), WeekDay::TUESDAY()->previous());
        $this->assertNull(WeekDay::MONDAY()->previous());
        $this->assertNull(WeekDay::SUNDAY()->next());
    }

    public function testRelativeOrder(): void
    {
        $this->assertTrue(WeekDay::MONDAY()->isBefore(WeekDay::TUESDAY()));
        $this->assertFalse(WeekDay::TUESDAY()->isBefore(WeekDay::MONDAY()));
        $this->assertTrue(WeekDay::TUESDAY()->isAfter(WeekDay::MONDAY()));
        $this->assertFalse(WeekDay::MONDAY()->isAfter(WeekDay::TUESDAY()));
    }

    public function testRandom(): void
    {
        $random = WeekDay::random();
        $this->assertInstanceOf(WeekDay::class, $random);
        $this->assertTrue(WeekDay::isValidName($random->name()));
    }

    public function testStringHelpers(): void
    {
        $this->assertSame('monday', WeekDay::MONDAY()->lowerName());
        $this->assertSame('monday', WeekDay::MONDAY()->camelName());
    }

    public function testToArray(): void
    {
        $array = WeekDay::toArray();
        $this->assertCount(7, $array);
        $this->assertSame('MONDAY', $array['MONDAY']);
    }

    public function testIsAnyOf(): void
    {
        $monday = WeekDay::MONDAY();
        $this->assertTrue($monday->isAnyOf([WeekDay::MONDAY(), WeekDay::FRIDAY()]));
        $this->assertFalse($monday->isAnyOf([WeekDay::TUESDAY(), WeekDay::FRIDAY()]));
    }

    public function testIsNoneOf(): void
    {
        $monday = WeekDay::MONDAY();
        $this->assertTrue($monday->isNoneOf([WeekDay::TUESDAY(), WeekDay::FRIDAY()]));
        $this->assertFalse($monday->isNoneOf([WeekDay::MONDAY(), WeekDay::FRIDAY()]));
    }
}
