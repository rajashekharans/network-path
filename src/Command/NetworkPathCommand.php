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

    /**
     * @param NetworkPathService $networkPathService
     */
    public function __construct(NetworkPathService $networkPathService)
    {
        parent::__construct();

        $this->networkPathService = $networkPathService;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('nw-path-test')
            ->addArgument('filepath', InputArgument::REQUIRED,'Set the path of csv containing network path info')
            ->setDescription('Runs Network Path Test ( nw-path-test <filepath> )');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if(!empty($input->getArgument('filepath'))){
            $this->networkPathService->createNetworkPathCollection($input->getArgument('filepath'));

            do {
                $helper = $this->getHelper('question');
                $question = new Question('Input: ');
                $question->setValidator(function ($answer) {
                    if(!empty($answer) && strcasecmp($answer, 'QUIT') == 0){
                        return $answer;
                    }
                    if (empty($answer) || count(explode(" ", strtoupper($answer))) !== 3) {
                        throw new \RuntimeException(
                          ' Output: Input error... Syntax:"[from] [to] [latency]", type "QUIT" to terminate command.'
                        );
                    }
                    return $answer;
                });
                $inputValue = $helper->ask($input, $output, $question);

                $this->networkPathService->setUpInputParameters($inputValue);
                $result = $this->networkPathService->evaluateNetworkPath();
                $output->writeln('Output: '.$result);
            } while(strcasecmp($inputValue, 'QUIT') != 0);

            $output->writeln('Ending command....');
            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }
}
