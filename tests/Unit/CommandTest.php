<?php

use Src\Kernel\Attributes\Command;

class CommandTest extends \PHPUnit\Framework\TestCase
{
    public function testValidCommandWithNoArgSignatureLat()
    {
        $command = new Command('/status');
        $this->assertSame('/status', $command->getCommandPattern());
    }

    public function testValidCommandWithNoArgSignatureCyrillic()
    {
        $command = new Command('/статус');
        $this->assertSame('/статус', $command->getCommandPattern());
    }

    public function testValidCommandWithUnderscoreWithNoArgSignatureLat()
    {
        $command = new Command('/request_status');
        $this->assertSame('/request_status', $command->getCommandPattern());
    }
    public function testValidCommandWithDashWithNoArgSignatureLat()
    {
        $command = new Command('/request-status');
        $this->assertSame('/request-status', $command->getCommandPattern());
    }

    public function testValidCommandWithUnderscoreWithNoArgSignatureRu()
    {
        $command = new Command('/заказ_статус');
        $this->assertSame('/заказ_статус', $command->getCommandPattern());
    }
    public function testValidCommandWithDashWithNoArgSignatureRu()
    {
        $command = new Command('/заказ-статус');
        $this->assertSame('/заказ-статус', $command->getCommandPattern());
    }

    public function testValidCommandWithArgSignature()
    {
        $command = new Command('/status {count} {order}');
        $this->assertSame('/status {count} {order}', $command->getCommandPattern());
    }

    public function testValidCommandWithArgSignatureWithWhitespace()
    {
        $command = new Command(' /status   {count}  {order}');
        $this->assertSame(' /status   {count}  {order}', $command->getCommandPattern());
    }
}