<?php

namespace StatusOnline\Server;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use StatusOnline\Console\Debug;
use StatusOnline\Dispatcher;
use StatusOnline\Storage\ClientStorage;
use StatusOnline\Storage\WebSocketConnections;

class Component implements MessageComponentInterface
{
    /**
     * @var WebSocketConnections
     */
    protected $webSocketConnections;
    /**
     * @var ClientStorage
     */
    protected $clientStorage;
    /**
     * @var Dispatcher
     */
    protected $commandDispatcher;

    /**
     * @inheritdoc
     */
    public function __construct ()
    {
        gc_enable();
        $this->webSocketConnections = new WebSocketConnections();
        $this->clientStorage = new ClientStorage();
        $this->commandDispatcher = new Dispatcher( [
            'clientStorage' => $this->clientStorage,
            'commandNamespace' => 'StatusOnline\\Command\\Ready\\',
            'commandFolder' => realpath( __DIR__ . '/../Command/Ready/' ),
        ] );
        Debug::line( 'Author: ' . \StatusOnline\AUTHOR );
        Debug::line( 'GitHub: ' . \StatusOnline\GITHUB );
        Debug::line();
        Debug::line( __CLASS__ . ': Started. Version: ' . \StatusOnline\VERSION );
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function onOpen ( ConnectionInterface $connection )
    {
        Debug::memory();
        Debug::line( __CLASS__ . ': Open [' );
        $this->webSocketConnections->add( $connection );
        Debug::line( ']' );
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function onClose ( ConnectionInterface $connection )
    {
        Debug::line( __CLASS__ . ': Close [' );
        $this->clientStorage->unregisterClient( $connection );
        $this->webSocketConnections->remove( $connection );
        Debug::line( ']' );
        gc_collect_cycles();
    }

    /**
     * @param ConnectionInterface $connection
     * @param \Exception          $exception
     */
    public function onError ( ConnectionInterface $connection, \Exception $exception )
    {
        Debug::line( __CLASS__ . ': Error [' );
        $this->clientStorage->unregisterClient( $connection );
        $this->webSocketConnections->remove( $connection );
        $connection->close();

        $connection = null;
        unset( $connection );

        Debug::line( ']' );
    }

    /**
     * @param ConnectionInterface $connection
     * @param string              $json
     */
    public function onMessage ( ConnectionInterface $connection, $json )
    {
        Debug::line( __CLASS__ . ': Received message [' );
        Debug::line( '-- Json: ' . $json );
        if ( ( $command = $this->commandDispatcher->create( $json, $connection ) ) ) {
            $command->process();

            $command = null;
            unset( $command );
        }
        Debug::line( ']' );
    }
}
