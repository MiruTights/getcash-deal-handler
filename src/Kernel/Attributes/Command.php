<?php

namespace Src\Kernel\Attributes;

use Src\Kernel\Helpers\CommandMap;

#[\Attribute]
readonly class Command
{
    public function __construct(public string $commandPattern)
    {
        if(!$this->isValidCommandPattern($commandPattern))
            throw new \InvalidArgumentException(
                'Command must start with "/". 
                Argument signature must be wrapped in "{}". 
                Command and argument signature must not contain special characters.'
            );
    }

    private function isValidCommandPattern(string $pattern): bool
    {
        $parts = preg_split('/\s+/', trim($pattern), 2);
        $command = $parts[0] ?? '';

        if (!str_starts_with($command, '/'))
            return false;

        $commandName = substr($command, 1);
        if(!preg_match('/^[\p{L}_][\p{L}\p{N}_-]*$/u', $commandName))
            return false;

        $argsPart = $parts[1] ?? '';
        if ($argsPart === '')
            return true;

        $tokens = preg_split('/\s+/', trim($argsPart));

        foreach ($tokens as $token)
            if (!$this->isValidArgPattern($token))
                return false;

        return true;
    }


    private function isValidArgPattern(string $token): bool
    {
        if (!str_starts_with($token, '{') || !str_ends_with($token, '}'))
            return false;

        $name = substr($token, 1, -1);

        if ($name === '')
            return false;

        return (bool) preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name);
    }

    public function getCommand():string
    {
        return new CommandMap($this->commandPattern)->getCommand();
    }

    public function getParameters():array
    {
        $commandMap = new CommandMap($this->commandPattern);
        return array_map(fn(string $signature) => substr($signature, 1, -1), $commandMap->getArguments());
    }

    public function getCommandPattern(): string
    {
        return $this->commandPattern;
    }

}