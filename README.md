# websocketio-server-php
SocketIO implementation using RFC6455-compliant websockets only. Built on top of Swoole Websocket Server.

## Requirements
- PHP >=7.2
- Swoole ^4.0

## Install
```
composer require sowe/websocketio
```

## Usage
```use Sowe\WebSocketIO\Server;
use Sowe\WebSocketIO\Client;

require_once __DIR__ . "/vendor/autoload.php";

$server = new Server('0.0.0.0', 9001);

$server->on('start', function() {
    echo "Server started!" . PHP_EOL;
});
$server->on('stop', function() {
    echo "Server stopped!" . PHP_EOL;
});
$server->on('connection', function(Client $client) {
    $client->on('disconnect', function() {
        echo "Client " . $client->getId() . " disconnected" . PHP_EOL;
    });
    $client->on('error', function($error) {
        echo "Client " . $client->getId() . " error: " . $error . PHP_EOL;
    });

    echo "New client connected " . $client->getId() . " from IP " . $client->getAddress() . PHP_EOL;
});
```
