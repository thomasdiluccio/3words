<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LintPhpstanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lint:phpstan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run phpstan';

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        exec('./vendor/bin/phpstan analyse --memory-limit=2G', $output);

        $isPassed = ($output[1] ?? '') === ' [OK] No errors';
        if ($isPassed) {
            $this->info(implode(PHP_EOL, $output));
            return 0;
        }

        $this->error(implode(PHP_EOL, $output));

        return 1;
    }

}
