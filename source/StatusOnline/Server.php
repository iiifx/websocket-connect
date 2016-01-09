<?php

namespace StatusOnline;

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use StatusOnline\Server\Component;

const VERSION = 'v0.3-beta';
const AUTHOR = 'Vitaliy IIIFX Khomenko <iiifx@yandex.com>';
const GITHUB = 'https://github.com/iiifx';

$loader = require __DIR__ . '/../../vendor/autoload.php';
$loader->add( '', realpath( __DIR__ . '/../' ) );

$wsServer = new WsServer( new Component() );
$wsServer->disableVersion( 0 );
$ioServer = IoServer::factory(
    new HttpServer( $wsServer ),
    Console\Params::get( 'port', 8080 )
);
$ioServer->run();
