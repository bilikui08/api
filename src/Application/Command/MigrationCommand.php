<?php

namespace Src\Application\Command;

use Symfony\Component\Console\Input\InputOption;
use Throwable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Src\Application\Handler\MigrationHandler;

class MigrationCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('run-migrations')
            ->setDescription('Run migrations')
            ->addOption('down', null, InputOption::VALUE_NONE, 'Activa los SQL down migrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $handler = new MigrationHandler();
            if ($input->getOption('down')) {
                $handler->setRunSqlDown(true);
            }
            $handler->run();
            $output->writeln('Run migrations successfully');
            return Command::SUCCESS;
        } catch (Throwable $t) {
            $output->writeln('Run migrations with errors');
            $output->writeln($t->getMessage());
            return Command::FAILURE;
        }
    }
}
