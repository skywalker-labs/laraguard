<?php

declare(strict_types=1);

namespace Skywalker\EnumTest;

use Skywalker\Enum\EnumSet;
use Skywalker\Enum\Exception\IllegalArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class EnumSetTest extends TestCase
{
    public function testConstructionWithInvalidEnumType(): void
    {
        $this->expectException(IllegalArgumentException::class);
        new EnumSet(stdClass::class);
    }

    public function testAddAndContains(): void
    {
        $set = new EnumSet(WeekDay::class);
        $this->assertFalse($set->contains(WeekDay::MONDAY()));
        $this->assertTrue($set->add(WeekDay::MONDAY()));
        $this->assertFalse($set->add(WeekDay::MONDAY()));
        $this->assertTrue($set->contains(WeekDay::MONDAY()));
    }

    public function testRemove(): void
    {
        $set = EnumSet::of(WeekDay::class, WeekDay::MONDAY(), WeekDay::FRIDAY());
        $this->assertTrue($set->contains(WeekDay::MONDAY()));
        $this->assertTrue($set->remove(WeekDay::MONDAY()));
        $this->assertFalse($set->remove(WeekDay::MONDAY()));
        $this->assertFalse($set->contains(WeekDay::MONDAY()));
    }

    public function testCount(): void
    {
        $set = new EnumSet(WeekDay::class);
        $this->assertSame(0, $set->count());
        $set->add(WeekDay::MONDAY());
        $this->assertSame(1, $set->count());
    }

    public function testAllOf(): void
    {
        $set = EnumSet::allOf(WeekDay::class);
        $this->assertSame(7, $set->count());
        $this->assertTrue($set->contains(WeekDay::SUNDAY()));
    }

    public function testIteratorIsSortedByOrdinal(): void
    {
        $set = EnumSet::of(WeekDay::class, WeekDay::FRIDAY(), WeekDay::MONDAY());
        $result = [];
        foreach ($set as $element) {
            $result[] = $element->name();
        }
        $this->assertSame(['MONDAY', 'FRIDAY'], $result);
    }

    public function testAddInvalidType(): void
    {
        $this->expectException(IllegalArgumentException::class);
        $set = new EnumSet(WeekDay::class);
        $set->add(Planet::EARTH());
    }

    public function testBitmaskConversion(): void
    {
        $set = EnumSet::of(WeekDay::class, WeekDay::MONDAY(), WeekDay::WEDNESDAY());
        // MONDAY = ordinal 0 = bit 1 (1 << 0)
        // WEDNESDAY = ordinal 2 = bit 4 (1 << 2)
        // Mask = 5
        $mask = $set->toBitmask();
        $this->assertSame(5, $mask);

        $newSet = EnumSet::fromBitmask(WeekDay::class, 5);
        $this->assertTrue($newSet->contains(WeekDay::MONDAY()));
        $this->assertTrue($newSet->contains(WeekDay::WEDNESDAY()));
        $this->assertFalse($newSet->contains(WeekDay::TUESDAY()));
        $this->assertSame(2, $newSet->count());
    }

    public function testFilter(): void
    {
        $set = EnumSet::allOf(WeekDay::class);
        $filtered = $set->filter(function ($day) {
            return $day->isAnyOf([WeekDay::SATURDAY(), WeekDay::SUNDAY()]);
        });
        $this->assertSame(2, $filtered->count());
        $this->assertTrue($filtered->contains(WeekDay::SATURDAY()));
    }

    public function testMap(): void
    {
        $set = EnumSet::of(WeekDay::class, WeekDay::MONDAY(), WeekDay::FRIDAY());
        $names = $set->map(function ($day) {
            return $day->name();
        });
        $this->assertSame(['MONDAY', 'FRIDAY'], $names);
    }

    public function testFirstAndLast(): void
    {
        $set = EnumSet::of(WeekDay::class, WeekDay::FRIDAY(), WeekDay::MONDAY());
        $this->assertSame(WeekDay::MONDAY(), $set->first());
        $this->assertSame(WeekDay::FRIDAY(), $set->last());

        $empty = new EnumSet(WeekDay::class);
        $this->assertNull($empty->first());
        $this->assertNull($empty->last());
    }

    public function testAnyAllNone(): void
    {
        $set = EnumSet::of(WeekDay::class, WeekDay::MONDAY(), WeekDay::WEDNESDAY());

        $this->assertTrue($set->any(fn($d) => $d->name() === 'MONDAY'));
        $this->assertFalse($set->any(fn($d) => $d->name() === 'FRIDAY'));

        $this->assertTrue($set->all(fn($d) => $d->ordinal() < 5));
        $this->assertFalse($set->all(fn($d) => $d->name() === 'MONDAY'));

        $this->assertTrue($set->none(fn($d) => $d->name() === 'FRIDAY'));
        $this->assertFalse($set->none(fn($d) => $d->name() === 'MONDAY'));
    }

    public function testFind(): void
    {
        $set = EnumSet::allOf(WeekDay::class);
        $found = $set->find(fn($d) => $d->ordinal() === 2);
        $this->assertSame(WeekDay::WEDNESDAY(), $found);

        $this->assertNull($set->find(fn($d) => $d->ordinal() === 99));
    }

    public function testReduce(): void
    {
        $set = EnumSet::of(WeekDay::class, WeekDay::MONDAY(), WeekDay::TUESDAY());
        // ordinals 0 and 1
        $sum = $set->reduce(fn($carry, $d) => $carry + $d->ordinal(), 0);
        $this->assertSame(1, $sum);
    }

    public function testRange(): void
    {
        $set = EnumSet::range(WeekDay::MONDAY(), WeekDay::WEDNESDAY());
        $this->assertSame(3, $set->count());
        $this->assertTrue($set->contains(WeekDay::TUESDAY()));

        // Reverse range
        $rev = EnumSet::range(WeekDay::WEDNESDAY(), WeekDay::MONDAY());
        $this->assertSame(3, $rev->count());
    }

    public function testSetTheory(): void
    {
        $setA = EnumSet::of(WeekDay::class, WeekDay::MONDAY(), WeekDay::TUESDAY());
        $setB = EnumSet::of(WeekDay::class, WeekDay::TUESDAY(), WeekDay::WEDNESDAY());

        $union = $setA->union($setB);
        $this->assertSame(3, $union->count());

        $intersect = $setA->intersect($setB);
        $this->assertSame(1, $intersect->count());
        $this->assertTrue($intersect->contains(WeekDay::TUESDAY()));

        $diff = $setA->diff($setB);
        $this->assertSame(1, $diff->count());
        $this->assertTrue($diff->contains(WeekDay::MONDAY()));

        $comp = $setA->complement();
        $this->assertSame(5, $comp->count());
        $this->assertFalse($comp->contains(WeekDay::MONDAY()));
    }
}
