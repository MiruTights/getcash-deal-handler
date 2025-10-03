<?php

namespace Src\Kernel;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ReflectionClass;
use ReflectionMethod;
use Src\Kernel\Attributes\Command;
use Src\Kernel\Attributes\SensitiveCommand;
use Src\Kernel\Helpers\CommandMap;
use Src\Kernel\Helpers\CommandReflection;
use Symfony\Component\Console\Exception\CommandNotFoundException;

abstract class AttributeCommandHandler implements CommandHandlerInterface
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @param LoggerInterface|null $logger
     */
    public function __construct(public ?LoggerInterface $logger = null)
    {
        $this->logger = $this->logger ?? new NullLogger();
    }

    /**
     * Returns target CommandProcessor class
     *
     * @return string
     */
    abstract protected function getCommandProcessorClass():string;

    /**
     * @param mixed $entity
     * @param string $inputCommand
     * @return void
     * @throws \Throwable
     */
    #[\Override]
    public function handle(mixed $entity, string $inputCommand):void
    {
        $this->logBeginHandling($inputCommand, $entity);

        $commandProcessor = new ($this->getCommandProcessorClass())($entity);
        $inputCommandMap = new CommandMap($inputCommand);
        $command = $inputCommandMap->getCommand();

        $reflection = new ReflectionClass($commandProcessor);

        $reflectionMethod = $this->getCommandReflection($reflection, $command);

        if(is_null($reflectionMethod))
        {
            $this->logCommandMethodToFoundError($command);
            throw new CommandNotFoundException('Command '.$command.' not found');
        }

        $commandReflection = $this->getCommandReflection($reflection, $command);
        $reflectionMethod = $commandReflection->method;
        $reflectionAttribute = $commandReflection->attribute;

        /**
         * @var Command $commandInstance
         */
        $commandInstance = $reflectionAttribute->newInstance();

        $instanceCommandParams = $commandInstance->getParameters();

        $this->logCommandMatch($command, $commandProcessor::class, $reflectionMethod->getName(), $instanceCommandParams);

        if($reflectionAttribute->getName() != SensitiveCommand::class)
            $this->logCommandArguments($commandInstance->getCommand(), $inputCommandMap->getArguments());


        $inputCommandArgs = $inputCommandMap->getArguments(count($instanceCommandParams));

        $argValues = [];
        foreach ($instanceCommandParams as $paramNumber => $instanceCommandParam)
        {
            if(isset($inputCommandArgs[$paramNumber]))
                $argValues[$instanceCommandParam] = $inputCommandArgs[$paramNumber];
        }

        $sortedArgs = [];
        foreach ($reflectionMethod->getParameters() as $param)
        {
            if(isset($argValues[$param->getName()]))
                $sortedArgs[] = $argValues[$param->getName()];
            elseif($param->isDefaultValueAvailable())
                $sortedArgs[] = $param->getDefaultValue();
            else
            {
                $this->logMissingArgument($command, $param->getName());
                throw new \InvalidArgumentException('Missing required parameter: ' . $param->getName());
            }
        }

        if($reflectionAttribute->getName() != SensitiveCommand::class)
            $this->logCommandPassedArguments($commandInstance->getCommand(), $sortedArgs);

        try {
            $reflectionMethod->invokeArgs($commandProcessor, $sortedArgs);
        } catch (\Throwable $exception){
            $this->logExecutionError($command, $exception);
            throw $exception;
        }
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param string $command
     * @return CommandReflection|null
     */
    private function getCommandReflection(ReflectionClass $reflectionClass, string $command):CommandReflection|null
    {
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
        {
            $attributes = $method->getAttributes(Command::class);
            if(empty($attributes))
                $attributes = $method->getAttributes(SensitiveCommand::class);

            foreach ($attributes as $attr)
            {
                /**
                 * @var Command $commandInstance
                 */
                $commandInstance = $attr->newInstance();
                if ($commandInstance->getCommand() === $command)
                    return new CommandReflection($method, $attr);
            }
        }
        return null;
    }


    /**
     * @param string $inputCommand
     * @param mixed $entity
     * @return void
     */
    private function logBeginHandling(string $inputCommand, mixed $entity):void
    {
        $this->logger->info('Handling command', [
            'input' => $inputCommand,
            'entity_type' => get_class($entity),
            'entity_properties' => (array)$entity
        ]);
    }

    /**
     * @param string $command
     * @param string $class
     * @param string $method
     * @return void
     */
    private function logCommandMatch(string $command, string $class, string $method, array $params):void
    {
        $this->logger->info('Command matched', [
            'command' => $command,
            'class' => $class,
            'method' => $method,
            'params' => $params
        ]);
    }

    /**
     * @param string $command
     * @param list<string> $arguments
     * @return void
     */
    private function logCommandArguments(string $command, array $arguments):void
    {
        $this->logger->debug('Command arguments', [
            'command' => $command,
            'arguments' => $arguments,
        ]);
    }

    /**
     * @param string $command
     * @param list<string> $arguments
     * @return void
     */
    private function logCommandPassedArguments(string $command, array $arguments):void
    {
        $this->logger->debug('Command passed arguments', [
            'command' => $command,
            'passed_arguments' => $arguments,
        ]);
    }

    /**
     * @param string $command
     * @param string $param
     * @return void
     */
    private function logMissingArgument(string $command, string $param):void
    {
        $this->logger->error('Missing required argument',[
            'command' => $command,
            'missing_parameter' => $param
        ]);
    }

    /**
     * @param string $command
     * @param \Throwable $exception
     * @return void
     */
    private function logExecutionError(string $command, \Throwable $exception):void
    {
        $this->logger->error('Execution error', [
            'command' => $command,
            'exception' => $exception
        ]);
    }

    /**
     * @param string $command
     * @return void
     */
    private function logCommandMethodToFoundError(string $command):void
    {
        $this->logger->error('Command not found', [
            'command' => $command,
        ]);
    }
}