<?php

namespace Sowe\WebSocketIO;

class Handler
{
    protected $callable;
    protected $requiredArguments;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;

        if ($callable instanceof \Closure) {
            $reflection = new \ReflectionFunction($callable);
        } elseif (is_string($callable)) {
            $parts = explode('::', $callable);
            if (sizeof($parts) > 1) {
                $reflection = new \ReflectionMethod(...$parts);
            } else {
                $reflection = new \ReflectionFunction($callable);
            }
        } elseif (!is_array($callable)) {
            $reflection = new ReflectionMethod($callable, '__invoke');
        } else {
            $reflection = new ReflectionMethod(...$callable);
        }

        $this->requiredArguments = $reflection->getNumberOfRequiredParameters();
    }

    public function handle(array $arguments)
    {
        $argc = sizeof($arguments);
        if ($argc < $this->requiredArguments) {
            throw new \Exception("Expected " . $this->requiredArguments . " arguments, got " . $argc);
        }
        $callable = $this->callable;
        $callable(...$arguments);
        return true;
    }
}
