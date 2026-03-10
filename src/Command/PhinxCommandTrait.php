<?php

namespace Mitsuki\Database\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

/**
 * Trait PhinxCommandTrait
 *
 * Provides shared utility methods for executing Phinx migration commands
 * from within the Symfony Console environment. It handles process management,
 * path normalization, and cross-platform compatibility.
 *
 * @package Mitsuki\Database\Command
 */
trait PhinxCommandTrait
{
    /**
     * Executes a Phinx command as a sub-process.
     *
     * This method locates the Phinx executable, builds the command arguments,
     * and streams the process output directly to the console in real-time.
     *
     * @param SymfonyStyle $io The IO styling helper for console feedback.
     * @param array $arguments List of arguments and options to pass to Phinx.
     * @return int Returns Command::SUCCESS (0) on success, or Command::FAILURE (1) on error.
     */
    private function runPhinx(SymfonyStyle $io, array $arguments): int
    {
        $phinxPath = $this->getPhinxPath();

        if (!$phinxPath) {
            $io->error('Phinx executable not found in vendor/bin/');
            return Command::FAILURE;
        }

        $command = array_merge([$phinxPath], $arguments);
        $process = new Process($command);

        $io->info("Executing: " . $process->getCommandLine());

        try {
            // Run the process and stream buffer output to the console
            $process->mustRun(function ($type, $buffer) use ($io) {
                $io->write($buffer);
            });

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Phinx command failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Locates the correct Phinx executable path for the current Operating System.
     *
     * Checks multiple common locations for both Unix-like (Linux/macOS) 
     * and Windows environments.
     *
     * @return string|null The absolute path to the Phinx executable, or null if not found.
     */
    private function getPhinxPath(): ?string
    {
        $basePaths = [
            'vendor/bin/phinx',           // Linux/macOS
            'vendor/bin/phinx.bat',       // Windows
            'vendor\\bin\\phinx',         // Windows alternative
            'vendor\\bin\\phinx.bat'      // Windows alternative
        ];

        foreach ($basePaths as $path) {
            $fullPath = $this->normalizePath($path);
            if (file_exists($fullPath)) {
                return $fullPath;
            }
        }

        return null;
    }

    /**
     * Normalizes a file path to match the current Operating System's directory separator.
     *
     * @param string $path The raw path to normalize.
     * @return string The normalized path using DIRECTORY_SEPARATOR.
     */
    private function normalizePath(string $path): string
    {
        return str_replace('/', DIRECTORY_SEPARATOR, realpath($path) ?: $path);
    }
}