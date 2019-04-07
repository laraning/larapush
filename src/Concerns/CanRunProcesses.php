<?php

namespace Laraning\Larapush\Concerns;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\RuntimeException;

trait CanRunProcesses
{
    protected function runProcess($command, $path = null)
    {
        $path = $path ?? getcwd();

        $process = (new Process($command, $path))->setTimeout(null);

        $process->run();

        if (! $process->isSuccessful()) {
            dd($process->getOutput(), $process->getErrorOutput());
            throw new RuntimeException($process->getErrorOutput());
        }

        return $process->getOutput();
    }
}
