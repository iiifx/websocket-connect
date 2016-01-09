<?php

namespace StatusOnline\Command\Ready;

use StatusOnline\Command\AbstractCommand;
use StatusOnline\Console\Debug;

class ChangeClientStatus extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    static public function identity ()
    {
        return [
            '_rid' => '',
            'change-status' => [
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
        if ( $this->storage->isRegisteredClient( $this->connection ) ) {
            $clientId = $this->storage->getRegisteredClientId( $this->connection );
            $clientStatus = $this->getMessageValue( 'change-status.status', 1 );
            $this->storage->changeClientStatus( $clientId, $clientStatus );
        }
        Debug::line( ']' );
        $this->connection = null;
        $this->storage = null;
    }
}
