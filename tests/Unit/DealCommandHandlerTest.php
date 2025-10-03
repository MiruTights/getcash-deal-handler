<?php

use Psr\Log\LoggerInterface;
use Mirutights\EntityInterfaces\DealEntityInterface;
use Mirutights\Handlers\DealCommandHandler;

class DealCommandHandlerTest extends \PHPUnit\Framework\TestCase
{
    public mixed $deal;
    public DealEntityInterface $dealEntity;
    protected function setUp(): void
    {

        $this->deal = new class
        {
            public string $status = 'Инициирован';
            public int $quantity;
            public string $location;
            public string $name = 'Заказ 23';
            public string $contact = '88005553535';
            public string $type = 'outOrder';
            public array $messages = [];
            public string $email;
            public string $closingReason;
        };


        $this->dealEntity = new class ($this->deal) implements DealEntityInterface
        {
            public function __construct(public mixed $deal)
            {
            }

            public function setStatus(string $status):void
            {
                $this->deal->status = $status;
            }

            public function setQuantity(int $quantity):void
            {
                $this->deal->quantity = $quantity;
            }

            public function setLocation(string $location):void
            {
                $this->deal->location = $location;
            }

            public function setName(string $name):void
            {
                $this->deal->name = $name;
            }

            public function setContact(string $contact):void
            {
                $this->deal->contact = $contact;
            }

            public function setType(string $type):void
            {
                $this->deal->type = $type;
            }

            public function setEmail(string $email):void
            {
                $this->deal->email = $email;
            }

            public function addContactServiceMessage():void
            {
                $parts = [];
                if(!empty($this->deal->contact))
                    $parts[] = $this->deal->contact;
                if(!empty($this->deal->email))
                    $parts[] = $this->deal->email;

                $this->deal->messages[] = implode(' ', $parts);
            }

            public function setClosingReason(string $reason): void
            {
                $this->deal->closingReason = $reason;
            }
        };
    }

    public function testStatusCommand()
    {
        $handler = new DealCommandHandler();
        $handler->handle($this->dealEntity, '/статус Принят');
        $this->assertSame('Принят', $this->deal->status);
    }

    public function testAcceptedCommand()
    {
        $handler = new DealCommandHandler();
        $handler->handle($this->dealEntity,'/принято 500 Лондон на дону');
        $this->assertSame('Лондон на дону', $this->deal->location);
        $this->assertSame(500, $this->deal->quantity);
    }

    public function testContactCommand()
    {
        $handler = new DealCommandHandler();
        $handler->handle($this->dealEntity, '/контакт');
        $this->assertSame(['88005553535'], $this->deal->messages);
    }

    public function testMailCommand()
    {
        $handler = new DealCommandHandler();
        $handler->handle($this->dealEntity, '/почта ex@ex.ex');
        $this->assertSame('ex@ex.ex', $this->deal->email);
    }

    public function testClosingReasonCommand()
    {
        $handler = new DealCommandHandler();
        $handler->handle($this->dealEntity, '/причина_закрытия удалена транзакция');
        $this->assertSame('удалена транзакция', $this->deal->closingReason);
    }

    public function testDealTypeCommand()
    {
        $handler = new DealCommandHandler();
        $handler->handle($this->dealEntity, '/тип_сделки Покупка');
        $this->assertSame('Покупка', $this->deal->type);
    }

    public function testSetContactCommand()
    {
        $handler = new DealCommandHandler();
        $handler->handle($this->dealEntity, '/задать_контакт 89001110101');
        $this->assertSame('89001110101', $this->deal->contact);
    }

    public function testSetNameCommand()
    {
        $handler = new DealCommandHandler();
        $handler->handle($this->dealEntity, '/имя kb0021 от 2025-09-02');
        $this->assertSame('kb0021 от 2025-09-02', $this->deal->name);
    }

    public function testHandlerLogger()
    {
        $inputCommand = '/принято 500 Лондон на Дону';

        $logger = Mockery::mock(LoggerInterface::class);

        $logger->expects('info')->withAnyArgs();

        $logger->expects('info')
            ->with('Command matched', [
                'command' => '/принято',
                'class' => \Mirutights\Processors\DealCommandProcessor::class,
                'method' => 'setAccepted',
            ]);

        $logger->expects('debug')
            ->with('Command arguments', [
                'command' => '/принято',
                'arguments' => [500, 'Лондон', 'на', 'Дону'],
            ]);

        $logger->expects('debug')
            ->with('Command passed arguments', [
                'command' => '/принято',
                'passed_arguments' => [
                    500,
                    'Лондон на Дону'
                ],
            ]);

        $handler = new DealCommandHandler($logger);
        $handler->handle($this->dealEntity, $inputCommand);

        $this->expectNotToPerformAssertions();
    }

    public function testHandlerLoggerSensitive()
    {
        $inputCommand = '/почта blob@blob.blob';

        $logger = Mockery::mock(LoggerInterface::class);

        $logger->expects('info')->withAnyArgs();
        $logger->expects('info')->withAnyArgs();

        $logger->shouldReceive('debug')
            ->with('Command arguments', Mockery::any())
            ->never();

        $logger->shouldReceive('debug')
            ->with('Command passed arguments', Mockery::any())
            ->never();


        $handler = new DealCommandHandler($logger);
        $handler->handle($this->dealEntity, $inputCommand);

        $this->expectNotToPerformAssertions();
    }
}