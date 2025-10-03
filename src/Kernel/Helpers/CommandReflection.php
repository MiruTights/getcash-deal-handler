<?php

namespace Src\Kernel\Helpers;

readonly class CommandReflection
{
    public function __construct(public \ReflectionMethod $method, public \ReflectionAttribute $attribute)
    {
    }
}