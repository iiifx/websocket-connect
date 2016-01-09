<?php

namespace StatusOnline\Command\Ready;

use StatusOnline\Command\AbstractCommand;
use StatusOnline\Console\Debug;

class RegisterClient extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    static public function identity ()
    {
        return [
            '_rid' => '',
            'register' => [
                'id' => '',
                'status' => '',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function process ()
    {
        Debug::line( __CLASS__ . ': Process [' );
        $clientId = $this->getMessageValue( 'register.id', false );
        if ( $clientId !== false ) {
            $clientStatus = $this->getMessageValue( 'register.status', 1 );
            $this->storage->registerClient( $this->connection, $clientId, $clientStatus );
        }
        Debug::line( ']' );
        $this->connection = null;
        $this->storage = null;
    }
}
