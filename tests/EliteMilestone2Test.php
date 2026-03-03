<?php

namespace Tests;

use Skywalker\Laraguard\Models\TwoFactorAuthenticationLog;
use Skywalker\Laraguard\Events\TwoFactorEnabled;
use Skywalker\Laraguard\Events\TwoFactorFailed;
use Tests\Stubs\UserTwoFactorStub;
use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Schema;

class EliteMilestone2Test extends TestCase
{
    use RegistersPackage;
    use DatabaseMigrations;
    use RunsPublishableMigrations;

    protected function setUp() : void
    {
        $this->afterApplicationCreated([$this, 'loadLaravelMigrations']);
        $this->afterApplicationCreated([$this, 'runPublishableMigration']);
        $this->afterApplicationCreated(function () {
             $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/2026_03_02_000000_create_two_factor_authentication_logs_table.php');
        });
        parent::setUp();
    }

    public function test_logs_two_factor_enabled_event(): void
    {
        $user = UserTwoFactorStub::create([
            'name'     => 'Log User',
            'email'    => 'log@test.com',
            'password' => 'secret',
        ]);

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();

        $this->assertDatabaseHas('two_factor_authentication_logs', [
            'authenticatable_id'   => $user->id,
            'authenticatable_type' => UserTwoFactorStub::class,
            'event'                => 'TwoFactorEnabled',
        ]);
    }

    public function test_logs_two_factor_failed_event_with_payload(): void
    {
        $user = UserTwoFactorStub::create([
            'name'     => 'Log User',
            'email'    => 'log@test.com',
            'password' => 'secret',
        ]);

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();
        
        $user->validateTwoFactorCode('123456');

        $this->assertDatabaseHas('two_factor_authentication_logs', [
            'authenticatable_id'   => $user->id,
            'event'                => 'TwoFactorFailed',
        ]);

        $log = TwoFactorAuthenticationLog::where('event', 'TwoFactorFailed')->first();
        $this->assertEquals('123456', $log->payload['code']);
    }
}
