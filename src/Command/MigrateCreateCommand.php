<?php

namespace Mitsuki\Database\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command to generate a new database migration file skeleton.
 *
 * This command bridges the Phinx 'create' functionality into the Mitsuki CLI,
 * ensuring new migration files are named correctly and placed in the defined
 * migrations directory.
 *
 * @author Zgeniuscoders <zgeniuscoders@gmail.com>
 * @package Mitsuki\Database\Command
 */
#[AsCommand(name: 'migrate:create', description: 'Creates a new migration')]
class MigrateCreateCommand extends Command
{
    /**
     * Shared logic for finding and executing the Phinx binary.
     * @see PhinxCommandTrait
     */
    use PhinxCommandTrait;

    /**
     * Configures the command's arguments and options.
     * * Defines the 'name' argument as a required parameter to label 
     * the migration class.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument(
            'name', 
            InputArgument::REQUIRED, 
            'The name of the migration (e.g. CreatePostTable)'
        );
    }

    /**
     * Executes the migration creation process.
     *
     * Retrieves the migration name from the input, passes it to the Phinx 
     * 'create' command, and provides visual feedback upon success.
     *
     * @param InputInterface $input The input interface for reading the migration name.
     * @param OutputInterface $output The output interface for console messaging.
     * @return int Command::SUCCESS (0) on success, or Command::FAILURE (1) on error.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');

        $exitCode = $this->runPhinx($io, ['create', $name]);

        if ($exitCode === Command::SUCCESS) {
            $io->success("Migration '$name' created successfully.");
        }

        return $exitCode;
    }
}