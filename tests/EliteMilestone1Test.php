<?php

namespace Tests;

use Skywalker\Laraguard\Models\TwoFactorAuthentication;
use Skywalker\Laraguard\Events\TwoFactorEnabled;
use Skywalker\Laraguard\Events\TwoFactorDisabled;
use Skywalker\Laraguard\Events\TwoFactorFailed;
use Tests\Stubs\UserTwoFactorStub;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class EliteMilestone1Test extends TestCase
{
    use RegistersPackage;
    use DatabaseMigrations;
    use RunsPublishableMigrations;

    protected function setUp() : void
    {
        $this->afterApplicationCreated([$this, 'loadLaravelMigrations']);
        $this->afterApplicationCreated([$this, 'runPublishableMigration']);
        parent::setUp();
    }

    public function test_laraguard_status_command(): void
    {
        Artisan::call('laraguard:status');
        $output = Artisan::output();

        $this->assertStringContainsString('Laraguard 2FA Status', $output);
        $this->assertStringContainsString('Total 2FA Records:   0', $output);
    }

    public function test_laraguard_reset_command(): void
    {
        $user = UserTwoFactorStub::create([
            'name'     => 'Elite User',
            'email'    => 'elite@test.com',
            'password' => 'secret',
        ]);

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();
        $this->assertTrue($user->hasTwoFactorEnabled());

        Artisan::call('laraguard:reset', [
            'user_id' => $user->id,
            '--model' => UserTwoFactorStub::class
        ]);

        $user->refresh();
        $this->assertFalse($user->hasTwoFactorEnabled());
        $this->assertStringContainsString('Two-Factor Authentication has been reset', Artisan::output());
    }

    public function test_dispatches_two_factor_enabled_event(): void
    {
        Event::fake();

        $user = UserTwoFactorStub::create([
            'name'     => 'Elite User',
            'email'    => 'elite@test.com',
            'password' => 'secret',
        ]);

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();

        Event::assertDispatched(TwoFactorEnabled::class, function ($event) use ($user) {
            return $event->user->is($user);
        });
    }

    public function test_dispatches_two_factor_disabled_event(): void
    {
        Event::fake();

        $user = UserTwoFactorStub::create([
            'name'     => 'Elite User',
            'email'    => 'elite@test.com',
            'password' => 'secret',
        ]);

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();
        $user->disableTwoFactorAuth();

        Event::assertDispatched(TwoFactorDisabled::class);
    }

    public function test_dispatches_two_factor_failed_event(): void
    {
        Event::fake();

        $user = UserTwoFactorStub::create([
            'name'     => 'Elite User',
            'email'    => 'elite@test.com',
            'password' => 'secret',
        ]);

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();
        
        // Invalid code
        $user->validateTwoFactorCode('123456');

        Event::assertDispatched(TwoFactorFailed::class, function ($event) use ($user) {
            return $event->user->is($user) && $event->code === '123456';
        });
    }
}
