<?php

namespace StatusOnline\Command;

use Ratchet\ConnectionInterface;
use StatusOnline\Storage\ClientStorage;

abstract class AbstractCommand
{
    /**
     * @var mixed[]
     */
    protected $message;
    /**
     * @var ConnectionInterface
     */
    protected $connection;
    /**
     * @var ClientStorage
     */
    protected $storage;

    /**
     * @param mixed[]             $message
     * @param ConnectionInterface $connection
     * @param ClientStorage       $storage
     */
    public function __construct ( $message, ConnectionInterface $connection, ClientStorage $storage )
    {
        $this->message = $message;
        $this->connection = $connection;
        $this->storage = $storage;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function getMessageValue ( $key, $default = null )
    {
        return $this->getValue( $this->message, $key, $default );
    }

    /**
     * @param mixed[] $array
     * @param string  $key
     * @param mixed   $default
     *
     * @return mixed
     */
    protected function getValue ( $array, $key, $default )
    {
        if ( ( $pos = strrpos( $key, '.' ) ) !== false ) {
            $array = $this->getValue( $array, substr( $key, 0, $pos ), $default );
            $key = substr( $key, $pos + 1 );
        }
        if ( is_array( $array ) ) {
            return array_key_exists( $key, $array ) ? $array[ $key ] : $default;
        }

        return $default;
    }

    /**
     * @return mixed
     */
    abstract public function process ();

    /**
     * @return mixed[]
     */
    static public function identity ()
    {
        return false;
    }
}
