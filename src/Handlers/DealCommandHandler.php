<?php

namespace Mirutights\Handlers;

use phpDocumentor\Reflection\Types\ClassString;
use Mirutights\EntityInterfaces\DealEntityInterface;
use Mirutights\Kernel\AttributeCommandHandler;
use Mirutights\Kernel\CommandHandlerInterface;
use Mirutights\Processors\DealCommandProcessor;

/**
 * @psalm-suppress UnusedClass
 */
class DealCommandHandler extends AttributeCommandHandler
{
    /**
     * @return string
     */
    #[\Override]
    protected function getCommandProcessorClass(): string
    {
        return DealCommandProcessor::class;
    }

    /**
     * @param DealEntityInterface $entity
     * @param string $inputCommand
     * @return void
     * @throws \Throwable
     */
    #[\Override]
    public function handle(mixed $entity, string $inputCommand):void
    {
       parent::handle($entity, $inputCommand);
    }

}