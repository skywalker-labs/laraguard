<?php

namespace Skywalker\Laraguard\Console;

use Illuminate\Console\Command;
use Skywalker\Laraguard\Models\TwoFactorAuthentication;

class LaraguardStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laraguard:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the status of Laraguard 2FA in the system';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $total2fa = TwoFactorAuthentication::count();
        $enabled2fa = TwoFactorAuthentication::whereNotNull('enabled_at')->count();

        $this->info("Laraguard 2FA Status");
        $this->line("--------------------");
        $this->line("Total 2FA Records:   {$total2fa}");
        $this->line("Enabled Users:       {$enabled2fa}");
        
        $totpConfig = config('laraguard.totp');
        $this->line("");
        $this->info("Current TOTP Configuration");
        foreach ($totpConfig as $key => $value) {
            $this->line("- " . ucfirst($key) . ": " . (is_array($value) ? implode(', ', $value) : $value));
        }

        return 0;
    }
}
