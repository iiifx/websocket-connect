<?php

namespace StatusOnline\Console;

class Params
{
    /**
     * @var string[]|null
     */
    static protected $params;

    /**
     * @return string[]
     */
    static protected function getParams ()
    {
        if ( self::$params === null ) {
            self::$params = [ ];
            if ( isset( $_SERVER[ 'argc' ], $_SERVER[ 'argv' ] ) && $_SERVER[ 'argc' ] > 1 ) {
                for ( $i = 1; $i <= $_SERVER[ 'argc' ]; $i++ ) {
                    preg_match( '/--(.*)=(.*)/', $_SERVER[ 'argv' ][ $i ], $parts );
                    if ( isset( $parts[ 1 ], $parts[ 2 ] ) ) {
                        self::$params[ $parts[ 1 ] ] = $parts[ 2 ];
                    }
                }
            }
        }

        return self::$params;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    static public function get ( $name, $default = null )
    {
        if ( isset( self::getParams()[ $name ] ) ) {
            return self::getParams()[ $name ];
        }

        return $default;
    }

    /**
     * @return string[]
     */
    static public function all ()
    {
        return self::getParams();
    }
}
