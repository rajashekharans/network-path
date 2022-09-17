<?php

namespace UserHierarchy\Tests\Integration\Command;

use NetworkPath\Command\NetworkPathCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use PHPUnit\Framework\TestCase;

class NetworkPathCommandTest extends TestCase
{
    public function testExecutePrintsSuccessOutput(): void
    {

        $command = new NetworkPathCommand();

        $tester = new CommandTester($command);
        $result = $tester->execute([]);

        $this->assertEquals(Command::SUCCESS, $result);
    }
}