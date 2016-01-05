<?php

require __DIR__ . '/vendor/autoload.php';

$userId = isset( $_GET[ 'id' ] ) ? (int) $_GET[ 'id' ] : 0;
