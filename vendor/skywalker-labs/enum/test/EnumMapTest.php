<?php

declare(strict_types=1);

namespace Skywalker\EnumTest;

use Skywalker\Enum\EnumMap;
use Skywalker\Enum\Exception\ExpectationException;
use Skywalker\Enum\Exception\IllegalArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class EnumMapTest extends TestCase
{
    public function testConstructionWithInvalidEnumType(): void
    {
        $this->expectException(IllegalArgumentException::class);
        new EnumMap(stdClass::class, 'string', false);
    }

    public function testUnexpectedKeyType(): void
    {
        $this->expectException(ExpectationException::class);
        $map = new EnumMap(WeekDay::class, 'string', false);
        $map->expect(Planet::class, 'string', false);
    }

    public function testUnexpectedValueType(): void
    {
        $this->expectException(ExpectationException::class);
        $map = new EnumMap(WeekDay::class, 'string', false);
        $map->expect(WeekDay::class, 'int', false);
    }

    public function testUnexpectedNullableValueType(): void
    {
        $this->expectException(ExpectationException::class);
        $map = new EnumMap(WeekDay::class, 'string', true);
        $map->expect(WeekDay::class, 'string', false);
    }

    public function testExpectedTypes(): void
    {
        $map = new EnumMap(WeekDay::class, 'string', true);
        $map->expect(WeekDay::class, 'string', true);
        $this->addToAssertionCount(1);
    }

    public function testSize(): void
    {
        $map = new EnumMap(WeekDay::class, 'string', true);
        $this->assertSame(0, $map->size());
        $map->put(WeekDay::MONDAY(), 'foo');
        $this->assertSame(1, $map->size());
    }

    public function testContainsValue(): void
    {
        $map = new EnumMap(WeekDay::class, 'string', true);
        $this->assertFalse($map->containsValue('foo'));
        $map->put(WeekDay::TUESDAY(), 'foo');
        $this->assertTrue($map->containsValue('foo'));
        $this->assertFalse($map->containsValue(null));
        $map->put(WeekDay::WEDNESDAY(), null);
        $this->assertTrue($map->containsValue(null));
    }

    public function testContainsKey(): void
    {
        $map = new EnumMap(WeekDay::class, 'string', true);
        $this->assertFalse($map->containsKey(WeekDay::TUESDAY()));
        $map->put(WeekDay::TUESDAY(), 'foo');
        $this->assertTrue($map->containsKey(WeekDay::TUESDAY()));
        $map->put(WeekDay::WEDNESDAY(), null);
        $this->assertTrue($map->containsKey(WeekDay::WEDNESDAY()));
    }

    public function testPutAndGet(): void
    {
        $map = new EnumMap(WeekDay::class, 'string', true);
        $map->put(WeekDay::TUESDAY(), 'foo');
        $map->put(WeekDay::FRIDAY(), null);
        $this->assertSame('foo', $map->get(WeekDay::TUESDAY()));
        $this->assertSame(null, $map->get(WeekDay::WEDNESDAY()));
        $this->assertSame(null, $map->get(WeekDay::FRIDAY()));
    }

    public function testPutInvalidKey(): void
    {
        $this->expectException(IllegalArgumentException::class);
        $map = new EnumMap(WeekDay::class, 'string', true);
        $map->put(Planet::MARS(), 'foo');
    }

    public static function invalidValues(): array
    {
        return [
            ['bool', null, false],
            ['bool', 0],
            ['boolean', 0],
            ['int', 2.4],
            ['integer', 5.3],
            ['float', 3],
            ['double', 7],
            ['string', 1],
            ['object', 1],
            ['array', 1],
            [stdClass::class, 1],
        ];
    }

    /**
     * @dataProvider invalidValues
     * @param mixed $value
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('invalidValues')]
    public function testPutInvalidValue(string $valueType, $value, bool $allowNull = true): void
    {
        $this->expectException(IllegalArgumentException::class);
        $map = new EnumMap(WeekDay::class, $valueType, $allowNull);
        $map->put(WeekDay::TUESDAY(), $value);
    }

    public static function validValues(): array
    {
        return [
            ['bool', null],
            ['mixed', 'foo'],
            ['mixed', 1],
            ['mixed', new stdClass()],
            ['bool', true],
            ['boolean', false],
            ['int', 1],
            ['integer', 4],
            ['float', 2.5],
            ['double', 6.4],
            ['string', 'foo'],
            ['object', new stdClass()],
            ['array', ['foo']],
            [stdClass::class, new stdClass()],
        ];
    }

    /**
     * @dataProvider validValues
     * @param mixed $value
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('validValues')]
    public function testPutValidValue(string $valueType, $value, bool $allowNull = true): void
    {
        $map = new EnumMap(WeekDay::class, $valueType, $allowNull);
        $map->put(WeekDay::TUESDAY(), $value);
        $this->addToAssertionCount(1);
    }

    public function testRemove(): void
    {
        $map = new EnumMap(WeekDay::class, 'string', true);
        $map->put(WeekDay::TUESDAY(), 'foo');
        $map->remove(WeekDay::TUESDAY());
        $map->remove(WeekDay::WEDNESDAY());
        $this->assertSame(null, $map->get(WeekDay::TUESDAY()));
        $this->assertSame(0, $map->size());
    }

    public function testClear(): void
    {
        $map = new EnumMap(WeekDay::class, 'string', true);
        $map->put(WeekDay::TUESDAY(), 'foo');
        $map->clear();
        $this->assertSame(null, $map->get(WeekDay::TUESDAY()));
        $this->assertSame(0, $map->size());
    }

    public function testEqualsWithSameInstance(): void
    {
        $map = new EnumMap(WeekDay::class, 'string', true);
        $this->assertTrue($map->equals($map));
    }

    public function testEqualsWithDifferentSize(): void
    {
        $mapA = new EnumMap(WeekDay::class, 'string', true);
        $mapB = new EnumMap(WeekDay::class, 'string', true);
        $mapB->put(WeekDay::MONDAY(), 'foo');

        $this->assertFalse($mapA->equals($mapB));
    }

    public function testEqualsWithDifferentValues(): void
    {
        $mapA = new EnumMap(WeekDay::class, 'string', true);
        $mapA->put(WeekDay::MONDAY(), 'foo');
        $mapB = new EnumMap(WeekDay::class, 'string', true);
        $mapB->put(WeekDay::MONDAY(), 'bar');

        $this->assertFalse($mapA->equals($mapB));
    }

    public function testEqualsWithDifferentConstants(): void
    {
        $mapA = new EnumMap(WeekDay::class, 'string', true);
        $mapA->put(WeekDay::MONDAY(), 'foo');
        $mapB = new EnumMap(WeekDay::class, 'string', true);
        $mapB->put(WeekDay::TUESDAY(), 'foo');

        $this->assertFalse($mapA->equals($mapB));
    }

    public function testValues(): void
    {
        $map = new EnumMap(WeekDay::class, 'string', true);
        $this->assertSame([], $map->values());

        $map->put(WeekDay::FRIDAY(), 'foo');
        $map->put(WeekDay::TUESDAY(), 'bar');
        $map->put(WeekDay::SUNDAY(), null);

        $this->assertSame(['bar', 'foo', null], $map->values());
    }

    public function testSerializeAndUnserialize(): void
    {
        $mapA = new EnumMap(WeekDay::class, 'string', true);
        $mapA->put(WeekDay::MONDAY(), 'foo');
        $mapB = unserialize(serialize($mapA));

        $this->assertTrue($mapA->equals($mapB));
    }

    public function testIterator(): void
    {
        $map = new EnumMap(WeekDay::class, 'string', true);
        $map->put(WeekDay::FRIDAY(), 'foo');
        $map->put(WeekDay::TUESDAY(), 'bar');
        $map->put(WeekDay::SUNDAY(), null);

        $result = [];

        foreach ($map as $key => $value) {
            $result[$key->ordinal()] = $value;
        }

        $this->assertSame([1 => 'bar', 4 => 'foo', 6 => null], $result);
    }

    public function testFilter(): void
    {
        $map = new EnumMap(WeekDay::class, 'string', true);
        $map->put(WeekDay::MONDAY(), 'work');
        $map->put(WeekDay::SUNDAY(), 'rest');

        $filtered = $map->filter(fn($v) => $v === 'rest');
        $this->assertSame(1, $filtered->size());
        $this->assertTrue($filtered->containsKey(WeekDay::SUNDAY()));
    }

    public function testMappers(): void
    {
        $map = new EnumMap(WeekDay::class, 'int', false);
        $map->put(WeekDay::MONDAY(), 10);
        $map->put(WeekDay::TUESDAY(), 20);

        $values = $map->mapValues(fn($v, $k) => $k->name() . ':' . $v);
        $this->assertSame(['MONDAY:10', 'TUESDAY:20'], $values);

        $keys = $map->mapKeys(fn($k, $v) => $k->name() . ':' . $v);
        $this->assertSame(['MONDAY:10', 'TUESDAY:20'], $keys);
    }

    public function testAnyAll(): void
    {
        $map = new EnumMap(WeekDay::class, 'int', false);
        $map->put(WeekDay::MONDAY(), 10);
        $map->put(WeekDay::TUESDAY(), 20);

        $this->assertTrue($map->any(fn($v) => $v > 15));
        $this->assertFalse($map->any(fn($v) => $v > 30));

        $this->assertTrue($map->all(fn($v) => $v > 5));
        $this->assertFalse($map->all(fn($v) => $v > 15));
    }

    public function testFirstLast(): void
    {
        $map = new EnumMap(WeekDay::class, 'string', false);
        $this->assertNull($map->first());
        $this->assertNull($map->last());

        $map->put(WeekDay::FRIDAY(), 'foo');
        $map->put(WeekDay::TUESDAY(), 'bar');

        $this->assertSame(WeekDay::TUESDAY(), $map->first());
        $this->assertSame(WeekDay::FRIDAY(), $map->last());
    }

    public function testKeys(): void
    {
        $map = new EnumMap(WeekDay::class, 'string', false);
        $map->put(WeekDay::FRIDAY(), 'foo');
        $map->put(WeekDay::TUESDAY(), 'bar');

        $this->assertSame([WeekDay::TUESDAY(), WeekDay::FRIDAY()], $map->keys());
    }
}
