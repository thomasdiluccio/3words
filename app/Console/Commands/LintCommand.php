<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LintCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lint';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all the linting commands';

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
     */
    public function handle(): int
    {
        $this->comment('Running PhpStan');
        $this->newLine();
        $response = $this->call('lint:phpstan');
        if ($response !== 0) {
            return 1;
        }

        $this->newLine(2);
        $this->comment('Running PhpMD');
        $response = $this->call('lint:phpmd');
        if ($response !== 0) {
            return 1;
        }

        $this->newLine(2);
        $this->info('You\'re great!');

        return 0;
    }

}
