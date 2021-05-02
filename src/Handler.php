<?php

namespace Sowe\WebSocketIO;

class Handler
{
    protected $closure;
    protected $requiredArguments;

    public function __construct(callable $closure)
    {
        $this->closure = $closure;
        $reflection = new \ReflectionFunction($closure);
        $this->requiredArguments = $reflection->getNumberOfRequiredParameters();
    }

    public function handle(array $arguments)
    {
        $argc = sizeof($arguments);
        if ($argc < $this->requiredArguments) {
            throw new \Exception("Expected " . $this->requiredArguments . " arguments, got " . $argc);
        }
        $closure = $this->closure;
        $closure(...$arguments);
        return true;
    }
}
