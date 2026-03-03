<?php


namespace Skywalker\Support\Tests\Http;

use Skywalker\Support\Tests\Stubs\DummyController;
use Skywalker\Support\Tests\TestCase;
use Illuminate\Routing\Router;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class     ControllerTest
 *
 * @author   Skywalker <skywalker@example.com>
 */
class ControllerTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupRoutes($this->app['router']);
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_do_dummy_stuff(): void
    {
        $response = $this->get(route('dummy::index'))
            ->assertSuccessful();

        static::assertEquals('Dummy', $response->getContent());

        $response = $this->get(route('dummy::get', ['super']))
            ->assertSuccessful();

        static::assertEquals('Super dummy', $response->getContent());
    }

    /** @test */
    public function it_can_throw_four_o_four_exception(): void
    {
        $response = $this->get(route('dummy::get', ['not-super']))
            ->assertStatus(404);

        static::assertInstanceOf(NotFoundHttpException::class, $response->exception);
        static::assertSame('Super dummy not found !', $response->exception->getMessage());
    }

    /**
     * Setup the routes.
     *
     * @param  \Illuminate\Routing\Router  $router
     */
    protected function setupRoutes(Router $router): void
    {
        $router->get('dummy', [DummyController::class, 'index'])
            ->name('dummy::index');

        $router->get('dummy/{slug}', [DummyController::class, 'show'])
            ->name('dummy::get');
    }
}
