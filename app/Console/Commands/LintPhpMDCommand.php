<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LintPhpMDCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lint:phpmd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run phpmd';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        exec('./vendor/phpmd/phpmd/src/bin/phpmd app/ text unusedcode', $output);
        $this->error(implode(PHP_EOL, $output));

        $isPassed = empty($output);
        if ($isPassed) {
            return 0;
        }

        return 1;
    }
}
