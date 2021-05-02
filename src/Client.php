<?php

namespace Sowe\WebSocketIO;

use Swoole\Http\Request;

class Client extends Emitter
{
    protected $id;
    protected $server;
    protected $socket;
    protected $address;

    public function __construct(Server $server, Request $request)
    {
        $this->id = bin2hex(random_bytes(12));
        $this->server = $server;
        $this->socket = $request->fd;
        $this->address = $request->server['remote_addr'];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSocket()
    {
        return $this->socket;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function emit($event, ...$data) {
        $event = new Event($event, ...$data);
        $this->server->push($this->socket, $event);
    }
}