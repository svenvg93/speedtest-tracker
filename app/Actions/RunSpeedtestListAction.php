<?php

namespace App\Actions;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class RunSpeedtestListAction
{
    public function execute(): string
    {
        $process = new Process(['speedtest', '-L', '-f', 'json']);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Output for debugging
        echo $process->getOutput();

        $output = $process->getOutput();
        $filePath = './storage/app/private/speedtest_servers.json';

        // Ensure directory exists
        if (! is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        // Save JSON output to the file
        file_put_contents($filePath, $output);

        return $filePath;
    }
}
