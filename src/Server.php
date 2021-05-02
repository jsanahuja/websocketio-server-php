<?php

namespace Sowe\WebSocketIO;

use Swoole\WebSocket\Server as WSServer;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;

class Server extends Emitter
{
    protected $server;
    protected $clients;

    public function __construct(string $ip, int $port, array $options = [])
    {
        $this->server = new WSServer(
            $ip,
            $port,
            SWOOLE_PROCESS,
            SWOOLE_SOCK_TCP | SWOOLE_SSL
        );
        $this->server->set($options);
        $this->clients = [];
    }

    public function push(int $socket, Event $event): void
    {
        $this->server->push($socket, $event->encode());
    }

    public function start(): void
    {
        $this->server->on('start', function (WSServer $server) {
            $this->trigger(new Event('start'));
        });

        $this->server->on('open', function (WSServer $server, Request $request) {
            $client = new Client($this, $request);
            $this->clients[$request->fd] = $client;
            $client->emit('connected');
            $this->trigger(new Event('connection', $client));
        });

        $this->server->on('message', function (WSServer $server, Frame $frame) {
            if (isset($this->clients[$frame->fd])) {
                $client = $this->clients[$frame->fd];
                try {
                    $event = Event::decode($frame->data);
                    $client->trigger($event);
                } catch (\Exception $e) {
                    $client->trigger(new Event('error', 'Invalid payload received:' . $e->getMessage()));
                }
            } else {
                $server->close($frame->fd);
            }
        });

        $this->server->on('close', function (WSServer $server, int $fd) {
            if (isset($this->clients[$fd])) {
                $this->clients[$fd]->trigger(new Event('disconnect'));
                unset($this->clients[$fd]);
            }
        });

        $this->server->start();
    }

    public function stop(): void
    {
        $this->trigger(new Event('stop'));
        $this->server->stop();
    }
}
