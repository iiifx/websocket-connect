<?php

use Ratchet\Server\IoServer;
use StatusOnline\Component;

$loader = require __DIR__ . '/vendor/autoload.php';
$loader->add( '', __DIR__ . '/source/' );

IoServer::factory(
    new Component(),
    8080
)->run();
