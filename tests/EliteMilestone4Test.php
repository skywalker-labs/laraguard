<?php

namespace Tests;

use Skywalker\Laraguard\Models\TwoFactorPasskey;
use Tests\Stubs\UserTwoFactorStub;
use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class EliteMilestone4Test extends TestCase
{
    use RegistersPackage;
    use DatabaseMigrations;
    use RunsPublishableMigrations;

    protected function setUp() : void
    {
        $this->afterApplicationCreated([$this, 'loadLaravelMigrations']);
        $this->afterApplicationCreated([$this, 'runPublishableMigration']);
        $this->afterApplicationCreated(function () {
             $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/2026_03_02_000001_create_two_factor_passkeys_table.php');
        });
        parent::setUp();
    }

    public function test_can_register_passkeys_to_user(): void
    {
        $user = UserTwoFactorStub::create([
            'name'     => 'Passkey User',
            'email'    => 'passkey@test.com',
            'password' => 'secret',
        ]);

        $user->passkeys()->create([
            'credential_id' => 'test-credential-id',
            'public_key'    => 'test-public-key',
            'nickname'      => 'My iPhone',
            'user_handle'   => bin2hex(random_bytes(16)),
        ]);

        $this->assertTrue($user->hasPasskeysEnabled());
        $this->assertCount(1, $user->passkeys);
        $this->assertEquals('test-credential-id', $user->passkeys->first()->credential_id);
    }

    public function test_passkey_routes_are_registered(): void
    {
        $this->app['config']->set('laraguard.confirm.view', 'laraguard::confirm');
        
        // Refresh routes
        $this->app['router']->getRoutes()->refreshNameLookups();

        $this->assertTrue($this->app['router']->has('2fa.passkey.register.options'));
        $this->assertTrue($this->app['router']->has('2fa.passkey.register'));
        $this->assertTrue($this->app['router']->has('2fa.passkey.login.options'));
    }
}
