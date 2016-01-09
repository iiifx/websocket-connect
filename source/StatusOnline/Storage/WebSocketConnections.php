<?php

namespace StatusOnline\Storage;

use Ratchet\ConnectionInterface;
use StatusOnline\Console\Debug;

class WebSocketConnections
{
    /**
     * @var \SplObjectStorage
     */
    protected $storage;

    /**
     * @inheritdoc
     */
    public function __construct ()
    {
        $this->storage = new \SplObjectStorage();
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function add ( ConnectionInterface $connection )
    {
        Debug::line( __CLASS__ . ': Add [' );
        $this->storage->attach( $connection );
        Debug::line( '-- WebSocket connection added' );
        Debug::line( ']' );
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function remove ( ConnectionInterface $connection )
    {
        Debug::line( __CLASS__ . ': Remove [' );
        $this->storage->detach( $connection );
        Debug::line( '-- WebSocket connection removed' );
        Debug::line( ']' );
    }
}
