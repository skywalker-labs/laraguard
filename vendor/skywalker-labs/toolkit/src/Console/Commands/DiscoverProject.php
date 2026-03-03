<?php

namespace Skywalker\Support\Console\Commands;

use Illuminate\Console\Command;
use Skywalker\Support\Discovery\ProjectMap;
use Illuminate\Support\Facades\File;

class DiscoverProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'toolkit:discover {--output=project-map.json : The output file path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a structured project map for AI context.';

    /**
     * Execute the console command.
     *
     * @param  \Skywalker\Support\Discovery\ProjectMap  $discovery
     * @return int
     */
    public function handle(ProjectMap $discovery)
    {
        $this->info('Scanning project structure...');

        $map = $discovery->generate();

        $output = $this->option('output');

        File::put(base_path($output), json_encode($map, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->info("Project map generated successfully at: {$output}");

        return 0;
    }
}
