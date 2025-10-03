<?php

namespace Src\Handlers;

use phpDocumentor\Reflection\Types\ClassString;
use Src\EntityInterfaces\DealEntityInterface;
use Src\Kernel\AttributeCommandHandler;
use Src\Kernel\CommandHandlerInterface;
use Src\Processors\DealCommandProcessor;

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