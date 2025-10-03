<?php

namespace Src\Processors;
use Src\EntityInterfaces\DealEntityInterface;
use Src\Handlers\DealCommandHandler;
use Src\Kernel\Attributes\Command;
use Src\Kernel\Attributes\SensitiveCommand;
use Src\Kernel\CommandProcessorInterface;

class DealCommandProcessor implements CommandProcessorInterface
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @param DealEntityInterface $deal
     */
    public function __construct(private DealEntityInterface $deal)
    {
    }


    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @param string $statusName
     * @return void
     */
    #[Command('/статус {statusName}')]
    public function setStatus(string $statusName): void
    {
        $this->deal->setStatus($statusName);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @param int $quantity
     * @param string $location
     * @return void
     */
    #[Command('/принято {quantity} {location}')]
    public function setAccepted(int $quantity, string $location): void
    {
        $this->deal->setLocation($location);
        $this->deal->setQuantity($quantity);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return void
     */
    #[Command('/контакт')]
    public function addContactServiceMessage(): void
    {
        $this->deal->addContactServiceMessage();
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @param string $email
     * @return void
     */
    #[SensitiveCommand('/почта {email}')]
    public function setEmail(string $email): void
    {
        $this->deal->setEmail($email);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @param string $reason
     * @return void
     */
    #[Command('/причина_закрытия {reason}')]
    public function setClosingReason(string $reason): void
    {
        $this->deal->setClosingReason($reason);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @param string $type
     * @return void
     */
    #[Command('/тип_сделки {type}')]
    public function setDealType(string $type): void
    {
        $this->deal->setType($type);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @param string $contact
     * @return void
     */
    #[Command('/задать_контакт {contact}')]
    public function setContact(string $contact): void
    {
        $this->deal->setContact($contact);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @param string $name
     * @return void
     */
    #[Command('/имя {name}')]
    public function setDealName(string $name): void
    {
        $this->deal->setName($name);
    }

}