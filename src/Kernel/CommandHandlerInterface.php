<?php

namespace Src\Kernel;


interface CommandHandlerInterface
{
    /**
     * Process operation by command
     *
     * @param mixed $entity
     * @param string $inputCommand
     * @return void
     */
    public function handle(mixed $entity, string $inputCommand):void;

}