<?php

namespace StatusOnline\Console;

class Debug
{
    /**
     * @var bool
     */
    static protected $isEnable;
    /**
     * @var string
     */
    static protected $tab = '    ';

    /**
     * @return bool
     */
    static protected function isEnable ()
    {
        if ( self::$isEnable === null ) {
            self::$isEnable = (bool) Params::get( 'debug', false );
        }

        return self::$isEnable;
    }

    /**
     * @param string $string
     */
    static public function out ( $string )
    {
        if ( self::isEnable() ) {
            echo $string;
        }
    }

    /**
     * @param string $string
     * @param bool   $autotabs
     */
    static public function line ( $string = '', $autotabs = true )
    {
        if ( self::isEnable() ) {
            $tabs = $autotabs ? self::getAutotabs( $string ) : '';
            if ( strpos( $string, '--' ) === 0 ) {
                $tabs .= self::$tab;
            }
            self::out( $tabs . $string . PHP_EOL );
        }
    }

    /**
     *
     */
    static public function memory ()
    {
        $current = number_format( memory_get_usage( true ) );
        $peak = number_format( memory_get_peak_usage( true ) );
        self::line();
        self::line( 'Memory: ' . $current . ' / ' . $peak . ' bytes' );
        self::line();
    }

    /**
     * @return string
     */
    static protected function getAutotabs ()
    {
        $backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 20 );
        while ( ( $level = array_pop( $backtrace ) ) ) {
            if ( isset( $level[ 'class' ] ) && $level[ 'class' ] === "StatusOnline\\Server\\Component" ) {
                break;
            }
        }
        $clearLevels = 0;
        while ( ( $level = array_shift( $backtrace ) ) ) {
            if ( isset( $level[ 'class' ] ) && $level[ 'class' ] !== __CLASS__ ) {
                $clearLevels++;
            }
        }
        $tabs = '';
        for ( $i = 0; $i < $clearLevels; $i++ ) $tabs .= self::$tab;

        return $tabs;
    }
}
