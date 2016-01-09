<?php

namespace StatusOnline;

use Ratchet\ConnectionInterface;
use StatusOnline\Command\AbstractCommand;
use StatusOnline\Console\Debug;
use StatusOnline\Storage\ClientStorage;

class Dispatcher
{
    /**
     * @var ClientStorage
     */
    protected $clientStorage;
    /**
     * @var string
     */
    protected $commandNamespace;
    /**
     * @var string
     */
    protected $commandFolder;
    /**
     * @var string
     */
    protected $commandList;

    /**
     * @param mixed[] $params
     */
    public function __construct ( $params )
    {
        $this->clientStorage = isset( $params[ 'clientStorage' ] ) ? $params[ 'clientStorage' ] : null;
        $this->commandNamespace = isset( $params[ 'commandNamespace' ] ) ? $params[ 'commandNamespace' ] : null;
        $this->commandFolder = isset( $params[ 'commandFolder' ] ) ? $params[ 'commandFolder' ] : null;
        if ( !isset( $this->clientStorage, $this->commandNamespace, $this->commandFolder ) ) {
            throw new \InvalidArgumentException( '' ); # @TODO
        }
    }

    /**
     * @param string              $json
     * @param ConnectionInterface $connection
     *
     * @return AbstractCommand
     */
    public function create ( $json, ConnectionInterface $connection )
    {
        Debug::line( __CLASS__ . ': Command create [' );
        if ( ( $message = json_decode( $json, true ) ) ) {
            if ( ( $class = $this->findIdentity( $message ) ) ) {
                Debug::line( '-- Command created' );
                Debug::line( ']' );

                return new $class( $message, $connection, $this->clientStorage );
            }
        }
        Debug::line( '-- [Bad message format]' );
        Debug::line( ']' );

        return false;
    }

    /**
     * @param mixed[] $message
     *
     * @return string|false
     */
    protected function findIdentity ( $message )
    {
        Debug::line( __CLASS__ . ': Find identity [' );
        foreach ( self::getCommandList() as $class => $identity ) {
            if ( $this->compareIdentityLevel( $identity, $message ) ) {
                Debug::line( '-- Identity found: ' . $class );
                Debug::line( ']' );

                return $class;
            }
        }
        Debug::line( '-- Identity not found' );
        Debug::line( ']' );

        return false;
    }

    /**
     * @param mixed[] $identity
     * @param mixed[] $message
     *
     * @return bool
     */
    protected function compareIdentityLevel ( $identity, $message )
    {
        foreach ( $identity as $key => $value ) {
            if ( !array_key_exists( $key, $message ) ) {
                # Такого ключа в сообщении нет
                return false;
            }
            if ( is_array( $value ) ) {
                # Следующий уровень вложенности проверяем отдельно
                if ( !$this->compareIdentityLevel( $value, $message[ $key ] ) ) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @return mixed[]
     */
    protected function getCommandList ()
    {
        if ( $this->commandList === null ) {
            Debug::line( __CLASS__ . ': Load commands [' );
            $this->commandList = [ ];
            if ( is_dir( $this->commandFolder ) && ( $handler = opendir( $this->commandFolder ) ) ) {
                while ( false !== ( $filename = readdir( $handler ) ) ) {
                    $filePath = $this->commandFolder . $filename;
                    if ( !in_array( $filename, [ '.', '..' ] ) && !is_dir( $filePath ) ) {
                        $className = preg_replace( '/\.php$/i', '', $filename );
                        $classFull = $this->commandNamespace . $className;
                        if ( class_exists( $classFull ) && method_exists( $classFull, 'identity' ) && ( $identity = $classFull::identity() ) ) {
                            Debug::line( '-- Load command: ' . $classFull );
                            $this->commandList[ $classFull ] = $identity;
                        }
                    }
                }
            }
            Debug::line( ']' );
        }

        return $this->commandList;
    }
}
