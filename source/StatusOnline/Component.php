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
        echo 'Open [' . PHP_EOL;
        $this->clientList->attach( $connection );
        echo '    -- Client List Attach' . PHP_EOL;
        echo ']' . PHP_EOL;
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function onClose ( ConnectionInterface $connection )
    {
        echo 'Close [' . PHP_EOL;
        $this->unregisterClient( $connection );
        echo '    -- Unregister Client' . PHP_EOL;
        $this->clientList->detach( $connection );
        echo '    -- Client List Detach' . PHP_EOL;
        echo ']' . PHP_EOL;
    }

    /**
     * @param ConnectionInterface $connection
     * @param Exception           $exception
     */
    public function onError ( ConnectionInterface $connection, Exception $exception )
    {
        echo 'Error [' . PHP_EOL;
        $this->unregisterClient( $connection );
        echo '    -- Unregister Client' . PHP_EOL;
        $connection->close();
        echo '    -- Close WS Connection' . PHP_EOL;
        echo ']' . PHP_EOL;
    }

    /**
     * @param ConnectionInterface $connection
     * @param string              $json
     */
    public function onMessage ( ConnectionInterface $connection, $json )
    {
        echo 'Message [' . PHP_EOL;
        echo '    -- ' . $json . PHP_EOL;
        $command = new Command( $json );
        if ( $command->isValid() ) {
            echo '    Command Valid' . PHP_EOL;
            if ( $command->isRegisterRequest() ) {
                echo '    -- Register Request ' . $command->getRegisterId() . ':' . $command->getRegisterStatus() . PHP_EOL;
                $this->registerClient(
                    $connection,
                    $command->getRegisterId(),
                    $command->getRegisterStatus()
                );
            }
            if ( $command->isStatusRequest() ) {
                echo '    -- Status Request ' . $command->getStatusId() . ' ->> ' . $this->getClientStatus( $command->getStatusId() ) . PHP_EOL;
                $connection->send( json_encode( [
                    'status' => $this->getClientStatus( $command->getStatusId() ),
                    'h' => $command->getHash(),
                ] ) );
            }
        }
        echo ']' . PHP_EOL;
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
        /** @noinspection PhpIllegalArrayKeyTypeInspection */
        $id = $this->idByConnection[ $connection ];
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
