<?php

namespace Mitsuki\Database\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command to initialize the migration environment.
 *
 * This command is typically run once per project to create the 
 * phinx.php or phinx.yml configuration file and the necessary 
 * directory structure for migrations and seeds.
 *
 * @author Zgeniuscoders <zgeniuscoders@gmail.com>
 * @package Mitsuki\Database\Command
 */
#[AsCommand(name: 'migrate:init', description: 'Init migration')]
class MigrationInit extends Command
{
    /**
     * Shared logic for locating and executing the Phinx binary.
     * @see PhinxCommandTrait
     */
    use PhinxCommandTrait;

    /**
     * Executes the Phinx initialization process.
     *
     * Invokes the 'init' command to generate the configuration 
     * file required by the Phinx migration engine.
     *
     * @param InputInterface $input The input interface for reading CLI arguments.
     * @param OutputInterface $output The output interface for console output.
     * @return int Command::SUCCESS (0) on success, or Command::FAILURE (1) on error.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Executes the bootstrap/initialization logic of Phinx
        return $this->runPhinx($io, ['init']);
    }
}
