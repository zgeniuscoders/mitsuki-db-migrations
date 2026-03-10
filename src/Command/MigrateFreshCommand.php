<?php

namespace Mitsuki\Database\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command to reset the database schema and re-run all migrations.
 *
 * This command performs a destructive operation by rolling back all 
 * existing migrations to version 0 (effectively dropping or emptying tables) 
 * and then running the migration suite from scratch.
 *
 * @author Zgeniuscoders <zgeniuscoders@gmail.com>
 * @package Mitsuki\Database\Command
 */
#[AsCommand(name: 'migrate:fresh', description: 'Reset and re-run all migrations')]
class MigrateFreshCommand extends Command
{
    /**
     * Shared logic for finding and executing the Phinx binary.
     * @see PhinxCommandTrait
     */
    use PhinxCommandTrait;

    /**
     * Executes the fresh migration process.
     *
     * The process flow is as follows:
     * 1. Display a warning and ask for user confirmation.
     * 2. Rollback all migrations using Phinx (target version 0).
     * 3. Execute all migrations to rebuild the schema.
     *
     * @param InputInterface $input The input interface for user confirmation.
     * @param OutputInterface $output The output interface for console feedback.
     * @return int Command::SUCCESS (0) if refreshed, or Command::FAILURE (1) on error.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->caution('This will drop all tables and re-run all migrations!');

        if (!$io->confirm('Are you sure you want to proceed?', false)) {
            $io->note('Operation cancelled by user.');
            return Command::SUCCESS;
        }

        $io->section('Rolling back all migrations...');
        
        $rollbackExitCode = $this->runPhinx($io, ['rollback', '-t', '0']);

        if ($rollbackExitCode !== Command::SUCCESS) {
            $io->error('Rollback failed. Fresh migration aborted.');
            return Command::FAILURE;
        }

        $io->section('Re-running migrations...');
        $migrateExitCode = $this->runPhinx($io, ['migrate']);

        if ($migrateExitCode === Command::SUCCESS) {
            $io->success('Database has been refreshed!');
        }

        return $migrateExitCode;
    }
}