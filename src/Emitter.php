<?php

namespace Sowe\WebSocketIO;

class Emitter
{
    protected $handers = [];

    public function on(string $event, callable $closure): void
    {
        $this->handlers[$event] = new Handler($closure);
    }

    public function off(string $event): void
    {
        if (isset($this->handlers[$event])) {
            unset($this->handlers[$event]);
        }
    }

    public function trigger(Event $event)
    {
        if (isset($this->handlers[$event->event])) {
            try {
                $this->handlers[$event->event]->handle($event->data);
            } catch (\Exception $e) {
                $this->trigger(new Event('error', "Error triggering '" . $event->event . "' event: ". $e->getMessage()));
            }
        }
    }
}
