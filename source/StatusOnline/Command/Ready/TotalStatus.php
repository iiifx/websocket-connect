<?php

namespace StatusOnline\Command\Ready;

use StatusOnline\Command\AbstractCommand;
use StatusOnline\Console\Debug;

class TotalStatus extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    static public function identity ()
    {
        return [
            '_rid' => '',
            'total-status' => '',
        ];
    }

    /**
     * @inheritdoc
     */
    public function process ()
    {
        Debug::line( __CLASS__ . ': Process [' );
        $totalData = $this->storage->getTotalStatusData();
        $json = json_encode( [
            '_rid' => $this->getMessageValue( '_rid', false ),
            'total-status' => [
                'count' => count( $totalData ),
                'list' => $totalData,
                'memory' => [
                    'current' => memory_get_usage( true ),
                    'peak' => memory_get_peak_usage( true ),
                ],
            ],
        ] );
        $this->connection->send( $json );
        Debug::line( '-- Response Json: ' . $json );
        Debug::line( ']' );
        $this->connection = null;
        $this->storage = null;
    }
}
