<?php

namespace Skywalker\Laraguard\Console;

use Illuminate\Console\Command;
use Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable;

class LaraguardResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laraguard:reset {user_id} {--model=App\Models\User}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Emergency reset of Two-Factor Authentication for a given user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $modelClass = $this->option('model');

        if (!class_exists($modelClass)) {
            $this->error("Model class [{$modelClass}] not found.");
            return 1;
        }

        $user = $modelClass::find($userId);

        if (!$user) {
            $this->error("User with ID [{$userId}] not found in [{$modelClass}].");
            return 1;
        }

        if (!$user instanceof TwoFactorAuthenticatable) {
            $this->error("User model does not implement [Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable].");
            return 1;
        }

        if ($user->hasTwoFactorEnabled()) {
            $user->disableTwoFactorAuth();
            $this->info("Two-Factor Authentication has been reset for User #{$userId}.");
        } else {
            $this->warn("User #{$userId} does not have Two-Factor Authentication enabled.");
        }

        return 0;
    }
}
