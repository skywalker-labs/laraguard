<?php


namespace Skywalker\Support\Tests\Providers;

use Skywalker\Support\Tests\TestCase;

/**
 * Class     RouteServiceProviderTest
 *
 * @author   Skywalker <skywalker@example.com>
 */
class RouteServiceProviderTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_map_routes(): void
    {
        $expectations = [
            'public::index'        => $this->baseUrl,
            'public::contact.show' => $this->baseUrl . '/contact',
            'public::contact.post' => $this->baseUrl . '/contact',
        ];

        foreach ($expectations as $route => $expected) {
            static::assertSame(route($route), $expected);
        }
    }

    /** @test */
    public function it_can_bind_routes(): void
    {
        $content = $this->get(route('public::pages.show', ['page-1234']))
            ->assertSuccessful()
            ->getContent();

        static::assertEquals('1234', $content);
    }
}
