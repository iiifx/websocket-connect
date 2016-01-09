<?php

namespace StatusOnline\Command\Ready;

use StatusOnline\Command\AbstractCommand;
use StatusOnline\Console\Debug;

class GetClientStatus extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    static public function identity ()
    {
        return [
            '_rid' => '',
            'get-status' => [
                'id' => '',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function process ()
    {
        Debug::line( __CLASS__ . ': Process [' );
        $clientId = $this->getMessageValue( 'get-status.id', false );
        list( $status, $connections ) = $this->storage->getClientStatusData( $clientId );
        $json = json_encode( [
            '_rid' => $this->getMessageValue( '_rid', false ),
            'status' => [
                'id' => $clientId,
                'status' => $status,
                'connections' => $connections,
            ],
        ] );
        $this->connection->send( $json );
        Debug::line( '-- Response Json: ' . $json );
        Debug::line( ']' );
        $this->connection = null;
        $this->storage = null;
    }
}
