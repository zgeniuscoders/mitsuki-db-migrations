<?php

namespace Mitsuki\Database\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command to revert previously executed database migrations.
 *
 * This command allows developers to undo the most recent migration or a 
 * specific number of migrations using the 'step' option, effectively 
 * rolling back the database schema.
 *
 * @author Zgeniuscoders <zgeniuscoders@gmail.com>
 * @package Mitsuki\Database\Command
 */
#[AsCommand(name: 'migrate:rollback', description: 'Rollback the last or N migrations')]
class MigrateRollbackCommand extends Command
{
    /**
     * Shared logic for finding and executing the Phinx binary.
     * @see PhinxCommandTrait
     */
    use PhinxCommandTrait;

    /**
     * Configures the command options.
     * * Adds the '--step' (or '-s') option to specify the depth of the rollback. 
     * Defaults to 1 if the option is not provided.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->addOption(
            'step', 
            's', 
            InputOption::VALUE_OPTIONAL, 
            'Number of migrations to rollback', 
            1
        );
    }

    /**
     * Executes the rollback process.
     *
     * Captures the step count from the input and passes it to the Phinx 
     * 'rollback' command.
     *
     * @param InputInterface $input The input interface for reading options.
     * @param OutputInterface $output The output interface for console feedback.
     * @return int Command::SUCCESS (0) on success, or Command::FAILURE (1) on error.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $step = $input->getOption('step');

        $io->info("Rolling back $step migration(s)...");

        // Build Phinx arguments list with the step flag
        $arguments = ['rollback', '-s', $step];

        return $this->runPhinx($io, $arguments);
    }
}