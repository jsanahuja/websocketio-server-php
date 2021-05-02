<?php

namespace Sowe\WebSocketIO;

class Event
{
    public $event;
    public $data;

    public function __construct(string $event, ...$data)
    {
        $this->event = $event;
        $this->data = $data;
    }

    public static function decode(string $payload): Event
    {
        $data = json_decode($payload, true);
        
        if (is_array($data) && sizeof($data) > 0) {
            $event = array_shift($data);
            if (is_string($event)) {
                return new self($event, ...$data);
            }
        }
        throw new \Exception("Invalid event format");
    }

    public function encode(): string
    {
        return json_encode(array_merge([$this->event], $this->data));
    }
}
