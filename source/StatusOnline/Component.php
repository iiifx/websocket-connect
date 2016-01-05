<?php

namespace StatusOnline;

use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

class Component implements MessageComponentInterface
{
    /**
     * @var SplObjectStorage
     */
    protected $clientList;

    /**
     * @var mixed[]
     */
    protected $statusList = [ ];

    /**
     * @inheritdoc
     */
    public function __construct ()
    {
        $this->clientList = new SplObjectStorage();
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function onOpen ( ConnectionInterface $connection )
    {
        $this->clientList->attach( $connection );
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function onClose ( ConnectionInterface $connection )
    {
        $this->unregisterClient( $connection );
        $this->clientList->detach( $connection );
    }

    /**
     * @param ConnectionInterface $connection
     * @param Exception           $exception
     */
    public function onError ( ConnectionInterface $connection, Exception $exception )
    {
        $this->unregisterClient( $connection );
        $connection->close();
    }

    /**
     * @param ConnectionInterface $connection
     * @param string              $json
     */
    public function onMessage ( ConnectionInterface $connection, $json )
    {
        $command = new Command( $json );
        if ( $command->isValid() ) {
            if ( $command->isRegisterRequest() ) {
                $this->registerClient(
                    $connection,
                    $command->getRegisterId(),
                    $command->getRegisterStatus()
                );
            }
            if ( $command->isStatusRequest() ) {
                $connection->send( $this->getClientStatus( $command->getStatusId() ) );
            }
        }
    }

    /**
     * @param ConnectionInterface $connection
     * @param int                 $id
     * @param string|null         $status
     */
    protected function registerClient ( ConnectionInterface $connection, $id, $status = null )
    {
    }

    /**
     * @param ConnectionInterface $connection
     */
    protected function unregisterClient ( ConnectionInterface $connection )
    {
    }

    /**
     * @param int $id
     *
     * @return string
     */
    protected function getClientStatus ( $id )
    {

    }
}
