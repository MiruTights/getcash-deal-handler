<?php

use Src\Kernel\Helpers\CommandMap;

class CommandMapTest extends \PHPUnit\Framework\TestCase
{
    public function testNoArgumentsGetCommand()
    {
        $commandMap = new CommandMap('/статус');
        $command = $commandMap->getCommand();
        $this->assertSame('/статус', $command);
    }

    public function testNoArgumentsWithSpacesGetCommand()
    {
        $commandMap = new CommandMap("\t  /статус   ");
        $command = $commandMap->getCommand();
        $this->assertSame('/статус', $command);
    }

    public function testOneArgumentGetArgs()
    {
        $commandMap = new CommandMap("/статус готово");
        $arguments = $commandMap->getArguments();
        $this->assertSame(['готово'], $arguments);
    }

    public function testOneArgumentWithSpacesGetArgs()
    {
        $commandMap = new CommandMap("\t\t /статус \t   готово  ");
        $arguments = $commandMap->getArguments();
        $this->assertSame(['готово'], $arguments);
    }

    public function testTwoArgumentsGetArgs()
    {
        $commandMap = new CommandMap("/статус готово офис");
        $arguments = $commandMap->getArguments();
        $this->assertSame(['готово', 'офис'], $arguments);
    }
    public function testTwoArgumentsWithSpacesGetArgs()
    {
        $commandMap = new CommandMap("\t\t /статус \tготово \t\tофис  ");
        $arguments = $commandMap->getArguments();
        $this->assertSame(['готово', 'офис'], $arguments);
    }

    public function testThreeLimitedArgumentsGetArgs()
    {
        $commandMap = new CommandMap("/статус готово офис 2025-05-01");
        $arguments = $commandMap->getArguments(2);
        $this->assertSame(['готово', 'офис 2025-05-01'], $arguments);
    }

    public function testThreeLimitedArgumentsWithSpacesGetArgs()
    {
        $commandMap = new CommandMap("\t/статус \tготово \t\tофис \t\t2025-05-01");
        $arguments = $commandMap->getArguments(2);
        $this->assertSame(['готово', 'офис 2025-05-01'], $arguments);
    }
}