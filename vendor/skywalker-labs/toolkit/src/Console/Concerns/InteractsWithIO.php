<?php

namespace Skywalker\Support\Console\Concerns;

trait InteractsWithIO
{
    /**
     * Display a success message in a box.
     *
     * @param  string  $message
     */
    protected function successBox(string $message): void
    {
        $this->output->block($message, 'SUCCESS', 'fg=black;bg=green', ' ', true);
    }

    /**
     * Display an error message in a box.
     *
     * @param  string  $message
     */
    protected function errorBox(string $message): void
    {
        $this->output->block($message, 'ERROR', 'fg=white;bg=red', ' ', true);
    }

    /**
     * Display an info message in a box.
     *
     * @param  string  $message
     */
    protected function infoBox(string $message): void
    {
        $this->output->block($message, 'INFO', 'fg=black;bg=cyan', ' ', true);
    }
}
