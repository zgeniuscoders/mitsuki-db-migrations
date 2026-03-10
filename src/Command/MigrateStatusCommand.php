<?php

namespace Mitsuki\Database\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command to display the current state of database migrations.
 *
 * This command provides a detailed report of all migration files, 
 * showing which have been applied (up) and which are still pending (down). 
 * It is a non-destructive read-only operation.
 *
 * @author Zgeniuscoders <zgeniuscoders@gmail.com>
 * @package Mitsuki\Database\Command
 */
#[AsCommand(name: 'migrate:status', description: 'Show migration status')]
class MigrateStatusCommand extends Command
{
    /**
     * Shared logic for finding and executing the Phinx binary.
     * @see PhinxCommandTrait
     */
    use PhinxCommandTrait;

    /**
     * Executes the status reporting process.
     *
     * Displays a title in the console and invokes the Phinx 'status' 
     * command to print the migration table.
     *
     * @param InputInterface $input The input interface for reading CLI arguments.
     * @param OutputInterface $output The output interface for console formatting.
     * @return int Command::SUCCESS (0) on success, or Command::FAILURE (1) on error.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Checking migration status...');

        // Delegates to Phinx to fetch and display the migration status table
        return $this->runPhinx($io, ['status']);
    }
}
