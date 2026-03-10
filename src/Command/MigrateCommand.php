<?php

namespace Mitsuki\Database\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command responsible for executing database migrations.
 *
 * This class acts as a bridge between the Symfony Console component 
 * and the Phinx migration engine, allowing database schema updates 
 * via the Mitsuki CLI.
 *
 * @author Zgeniuscoders <zgeniuscoders@gmail.com>
 * @package Mitsuki\Database\Command
 */
#[AsCommand(name: 'migrate', description: 'Migrate the database')]
class MigrateCommand extends Command
{
    /**
     * Includes shared logic for handling Phinx execution.
     * @see PhinxCommandTrait::runPhinx()
     */
    use PhinxCommandTrait;

    /**
     * Executes the migration command.
     *
     * Initializes the SymfonyStyle UI and delegates the migration 
     * process to the Phinx engine.
     *
     * @param InputInterface $input The input interface for reading CLI arguments.
     * @param OutputInterface $output The output interface for writing to the console.
     * * @return int Command exit code: 0 for success, 1 or higher for failure.
     * @throws \RuntimeException If the Phinx environment cannot be initialized.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        return $this->runPhinx($io, ['migrate']);
    }
}