<?php


namespace Skywalker\Support\Tests\Http;

use Skywalker\Support\Tests\Stubs\FormRequestController;
use Skywalker\Support\Tests\TestCase;
use Illuminate\Routing\Router;

/**
 * Class     FormRequestTest
 *
 * @author   Skywalker <skywalker@example.com>
 */
class FormRequestTest extends TestCase
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
    public function it_can_check_validation(): void
    {
        $this->post('form-request')
            ->assertStatus(302)
            ->assertRedirect('/');

        $response = $this->post('form-request', [
            'name'  => 'Skywalker',
            'email' => 'skywalker@example.com',
        ]);

        $response
            ->assertSuccessful()
            ->assertJson([
                'name'  => 'SKYWALKER',
                'email' => 'skywalker@example.com',
            ]);
    }

    /** @test */
    public function it_can_sanitize(): void
    {
        $response = $this->post('form-request', [
            'name'  => 'Skywalker',
            'email' => ' SKYWALKER@example.COM ',
        ]);

        $response
            ->assertSuccessful()
            ->assertJson([
                'name'  => 'SKYWALKER',
                'email' => 'skywalker@example.com',
            ]);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Setup the routes.
     *
     * @param  \Illuminate\Routing\Router  $router
     */
    private function setupRoutes(Router $router): void
    {
        $router->post('form-request', [FormRequestController::class, 'form'])
            ->name('form-request');
    }
}
