<?php

use Ratchet\Http\HttpServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

require __DIR__.'/vendor/autoload.php';

$chatComponent = new class implements MessageComponentInterface {

    /**
     * @var SplObjectStorage
     */
    public $connections;

    public function __construct()
    {
        $this->connections = new SplObjectStorage();
    }

    function onOpen(ConnectionInterface $conn)
    {

        echo "New connection ".PHP_EOL;
        $this->connections->attach($conn);
        $size = (string)$this->connections->count();
        echo "New connection ({$size})".PHP_EOL;
        $conn->send($size);
    }

    function onClose(ConnectionInterface $conn)
    {
        echo "Connection closed ".PHP_EOL;
        $this->connections->detach($conn);
    }

    function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error on Server ".PHP_EOL;
        echo $e->getTraceAsString().PHP_EOL;
    }

    function onMessage(ConnectionInterface $from, $msg)
    {
        echo "Messeger received ".PHP_EOL;
        echo "'".$msg."'".PHP_EOL;
        echo "------".PHP_EOL;

        /** @var ConnectionInterface $connection */
        foreach ($this->connections as $connection) {
            if ($from !== $connection) {
                $connection->send((string)$msg);
            }
        }
    }
};

$port = 443;
$server = IoServer::factory(
    new HttpServer(
        new WsServer($chatComponent)
    ),
    $port
);

echo "App running at localhost::{$port}";
echo PHP_EOL;

$server->run();
