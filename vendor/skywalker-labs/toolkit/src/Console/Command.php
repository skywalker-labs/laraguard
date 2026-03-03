<?php

namespace Skywalker\Support\Console;

use Illuminate\Console\Command as IlluminateCommand;
use Symfony\Component\Console\Helper\TableSeparator;

/**
 * Class     Command
 *
 * @author   Skywalker <skywalker@example.com>
 */
abstract class Command extends \Illuminate\Console\Command
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Execute the console command.
     *
     * @return int
     */
    abstract public function handle(): int;

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Create a new table separator instance.
     *
     * @return \Symfony\Component\Console\Helper\TableSeparator
     */
    protected function tableSeparator(): TableSeparator
    {
        return new TableSeparator;
    }

    /**
     * Display a framed information box.
     *
     * @param  string  $text
     *
     * @return void
     */
    protected function frame(string $text): void
    {
        $line = '+' . str_repeat('-', strlen($text) + 4) . '+';

        $this->info($line);
        $this->info("|  $text  |");
        $this->info($line);
    }
}
