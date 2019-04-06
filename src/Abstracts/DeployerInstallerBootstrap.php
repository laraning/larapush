<?php

namespace Laraning\Larapush\Abstracts;

use Illuminate\Console\Command;
use Laraning\Larapush\Concerns\CanRunProcesses;
use Laraning\Larapush\Concerns\SharedInstallerActions;
use Laraning\Larapush\Concerns\SimplifiesConsoleOutput;
use Laraning\Larapush\Concerns\ValidatesConsoleArguments;

abstract class DeployerInstallerBootstrap extends Command
{
    use CanRunProcesses;
    use SharedInstallerActions;
    use SimplifiesConsoleOutput;
    use ValidatesConsoleArguments;

    protected $steps;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Quick way to clear the screen :)
        echo "\033[2J\033[;H";

        $this->info(ascii_title());
    }
}
