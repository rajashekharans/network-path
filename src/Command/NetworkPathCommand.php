<?php

namespace NetworkPath\Command;

use UserHierarchy\DTO\UserHierarchyCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NetworkPathCommand extends Command
{

    public function __construct()
    {
        parent::__construct();

    }

    protected function configure(): void
    {
        $this
            ->setName('app:run')
            ->setDescription('Runs Network Path Test');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::SUCCESS;
    }

}
