<?php

namespace Mirutights\Kernel\Helpers;

readonly class CommandMap
{
    /**
     * @param string $command
     */
    public function __construct(public string $command)
    {
    }

    /**
     * Returns main signature of the command
     *
     * @return string
     */
    public function getCommand():string
    {
        return (string)strtok($this->command, " \t\n\r");
    }

    /**
     * Returns list array with command arguments
     *
     * @param int|null $maxCount
     * @return string[]
     */
    public function getArguments(?int $maxCount = null):array
    {
        $parts = preg_split('/\s+/', trim($this->command), 2);

        if (count($parts) < 2 || $maxCount === 0)
            return [];

        $argsString = $parts[1];
        $words = preg_split('/\s+/', $argsString);

        if (count($words) <= $maxCount)
            return $words;

        $result = array_slice($words, 0, $maxCount - 1);

        $remaining = array_slice($words, $maxCount - 1);
        $result[] = implode(' ', $remaining);
        return $result;
    }

}