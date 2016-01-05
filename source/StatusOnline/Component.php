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
     * @var SplObjectStorage
     */
    protected $idByConnection;
    /**
     * @var mixed[]
     */
    protected $statusById;

    /**
     * @inheritdoc
     */
    public function __construct ()
    {
        $this->clientList = new SplObjectStorage();
        $this->idByConnection = new SplObjectStorage();
        $this->statusById = [ ];
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
    protected function registerClient ( ConnectionInterface $connection, $id, $status )
    {
        if ( $id > 0 ) {
            # Регистрируем в списке ID
            if ( !isset( $this->statusById[ $id ] ) ) {
                $this->statusById[ $id ] = [
                    'status' => null,
                    'connections' => [ ],
                ];
            }
            $this->statusById[ $id ][ 'status' ] = $status;
            $this->statusById[ $id ][ 'connections' ][] = $connection;
            # Регистрируем в списке соединений
            $this->idByConnection->attach( $connection, $id );
        }
    }

    /**
     * @param ConnectionInterface $connection
     */
    protected function unregisterClient ( ConnectionInterface $connection )
    {
        # Получаем ID
        $id = $this->idByConnection->offsetGet( $connection );
        if ( $id > 0 ) {
            # Удаляем со списка ID
            if ( isset( $this->statusById[ $id ] ) ) {
                foreach ( $this->statusById[ $id ][ 'connections' ] as $i => $conn ) {
                    if ( $conn === $connection ) {
                        unset( $this->statusById[ $id ][ 'connections' ][ $i ] );
                    }
                }
                # Удаляем полностью, если пусто
                if ( !$this->statusById[ $id ][ 'connections' ] ) {
                    unset( $this->statusById[ $id ] );
                }
            }
        }
        # Удаляем со списка соединений
        $this->idByConnection->detach( $connection );
    }

    /**
     * @param int $id
     *
     * @return string|false
     */
    protected function getClientStatus ( $id )
    {
        if ( isset( $this->statusById[ $id ][ 'status' ] ) ) {
            return $this->statusById[ $id ][ 'status' ];
        }
        return false;
    }
}
