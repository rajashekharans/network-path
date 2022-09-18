<?php

namespace NetworkPath\Command;

use NetworkPath\Service\NetworkPathService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class NetworkPathCommand extends Command
{

    /**
     * @var NetworkPathService
     */
    private $networkPathService;

    public function __construct(NetworkPathService $networkPathService)
    {
        parent::__construct();

        $this->networkPathService = $networkPathService;
    }

    protected function configure(): void
    {
        $this
            ->setName('app:run')
            ->addArgument('filename', InputArgument::REQUIRED,'Set the path of csv containing network path info')
            ->setDescription('Runs Network Path Test');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if(!empty($input->getArgument('filename'))){
            $this->networkPathService->createNetworkPathCollection($input->getArgument('filename'));

            $helper = $this->getHelper('question');
            $question = new Question('Input: ');
            $inputValue = $helper->ask($input, $output, $question);
            while (strtolower($inputValue) != 'quit') {
                $validationResult = $this->networkPathService->setUpInputParameters($inputValue);
                if($validationResult) {
                    $result = $this->networkPathService->evaluateNetworkPath();
                    $output->writeln('Output: '.$result);
                } else {
                    $output->writeln('Output: Input error... Syntax:"[from] [to] [latency]", type "QUIT" to terminate command.');
                }

                $question = new Question('Input: ');
                $inputValue = $helper->ask($input, $output, $question);
            }
            $output->writeln('Ending command....');
            return Command::SUCCESS;
        }


        return Command::FAILURE;
    }

}
