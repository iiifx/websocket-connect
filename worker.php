<?php

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use StatusOnline\Component;

$loader = require __DIR__ . '/vendor/autoload.php';
$loader->add( '', __DIR__ . '/source/' );

$ws = new WsServer( new Component() );
$ws->disableVersion( 0 );
$server = IoServer::factory(
    new HttpServer( $ws ),
    8081
);
$server->run();
