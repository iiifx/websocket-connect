<?php

namespace StatusOnline\Storage;

use Ratchet\ConnectionInterface;
use StatusOnline\Console\Debug;

class ClientStorage
{
    /**
     * @var mixed[]
     */
    protected $clientList = [ ];

    /**
     * @param ConnectionInterface $connection
     *
     * @return bool
     */
    public function isRegisteredClient ( ConnectionInterface $connection )
    {
        if ( isset( $connection->clientId ) ) {
            return true;
        }

        return false;
    }

    /**
     * @param ConnectionInterface $connection
     *
     * @return int|bool
     */
    public function getRegisteredClientId ( ConnectionInterface $connection )
    {
        if ( $this->isRegisteredClient( $connection ) ) {
            /** @noinspection PhpUndefinedFieldInspection */
            return $connection->clientId;
        }

        return false;
    }

    /**
     * @param ConnectionInterface $connection
     * @param int                 $clientId
     * @param int|string          $clientStatus
     */
    public function registerClient ( ConnectionInterface $connection, $clientId, $clientStatus )
    {
        Debug::line( __CLASS__ . ': Register client [' );
        if ( $this->isRegisteredClient( $connection ) ) {
            Debug::line( '-- Client is already registered' );
            $this->unregisterClient( $connection );
        }
        /** @noinspection PhpUndefinedFieldInspection */
        $connection->clientId = $clientId;
        if ( !isset( $this->clientList[ $clientId ] ) ) {
            $this->clientList[ $clientId ] = [ ];
        }
        $this->clientList[ $clientId ][ 'status' ] = $clientStatus;
        $this->clientList[ $clientId ][ 'connections' ][ ] = $connection;
        Debug::line( '-- Client registered: [id:' . $clientId . '] [status:' . $clientStatus . ']' );
        Debug::line( ']' );
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function unregisterClient ( ConnectionInterface $connection )
    {
        Debug::line( __CLASS__ . ': Unregister client [' );
        if ( $this->isRegisteredClient( $connection ) ) {
            $clientId = $this->getRegisteredClientId( $connection );
            if ( isset( $this->clientList[ $clientId ][ 'connections' ] ) ) {
                foreach ( $this->clientList[ $clientId ][ 'connections' ] as $i => &$conn ) {
                    if ( $conn === $connection ) {
                        $this->clientList[ $clientId ][ 'connections' ][ $i ] = null;

                        $this->clientList[ $clientId ][ 'connections' ][ $i ] = null;
                        unset( $this->clientList[ $clientId ][ 'connections' ][ $i ] );

                        Debug::line( '-- Client unregistered: [id:' . $clientId . ']' );
                    }
                }
                $clientCount = count( $this->clientList[ $clientId ][ 'connections' ] );
                Debug::line( '-- ' . $clientCount . ' clients currently: [id:' . $clientId . ']' );
                if ( !count( $this->clientList[ $clientId ][ 'connections' ] ) ) {

                    $this->clientList[ $clientId ] = null;
                    unset( $this->clientList[ $clientId ] );

                }
            }
        }
        Debug::line( ']' );
    }

    /**
     * @param int        $clientId
     * @param int|string $clientStatus
     */
    public function changeClientStatus ( $clientId, $clientStatus )
    {
        Debug::line( __CLASS__ . ': Change client status [' );
        if ( isset( $this->clientList[ $clientId ][ 'status' ] ) ) {
            $this->clientList[ $clientId ][ 'status' ] = $clientStatus;
            Debug::line( '-- New status: [id:' . $clientId . '] [status:' . $clientStatus . ']' );
        } else {
            Debug::line( '-- Client not found: [id:' . $clientId . ']' );
        }
        Debug::line( ']' );
    }

    /**
     * @param int $clientId
     *
     * @return string[]
     */
    public function getClientStatusData ( $clientId )
    {
        Debug::line( __CLASS__ . ': Get client status data [' );
        if ( isset( $this->clientList[ $clientId ][ 'status' ] ) ) {
            $status = $this->clientList[ $clientId ][ 'status' ];
            $connections = isset( $this->clientList[ $clientId ][ 'connections' ] ) ?
                count( $this->clientList[ $clientId ][ 'connections' ] ) : 0;
            Debug::line( '-- Client status data:' );
            Debug::line( '-- Id: ' . $clientId );
            Debug::line( '-- Status: ' . $status );
            Debug::line( '-- Connections: ' . $connections );
            Debug::line( ']' );

            return [
                $status,
                $connections,
            ];
        }
        Debug::line( '-- Client status data not found' );
        Debug::line( ']' );

        return [
            false,
            false,
        ];
    }

    /**
     * @return mixed[]
     */
    public function getTotalStatusData ()
    {
        Debug::line( __CLASS__ . ': Get total status data [' );
        $result = [ ];
        foreach ( $this->clientList as $clientId => &$data ) {
            $result[ $clientId ][ 'status' ] = $data[ 'status' ];
            $result[ $clientId ][ 'connections' ] = count( $data[ 'connections' ] );
        }
        Debug::line( '-- All status data obtained' );
        Debug::line( ']' );

        return $result;
    }
}
