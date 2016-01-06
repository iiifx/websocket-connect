<?php

namespace StatusOnline;

class Command
{
    const HASH = 'h';
    const COMMAND_REGISTER = 'register';
    const COMMAND_STATUS = 'status';
    const VALUE_ID = 'id';
    const VALUE_STATUS = 'status';

    const DEFAULT_STATUS = 'O';

    /**
     * [
     *     # Это команда зарегистрировать статус клиента
     *     self::COMMAND_REGISTER => [
     *         self::VALUE_ID => 1,
     *         self::VALUE_STATUS => 'O',
     *     ],
     *
     *     # Это команда определить статус клиента
     *     self::COMMAND_STATUS => [
     *         self::VALUE_ID => 1,
     *     ],
     * ]
     *
     * @var array
     */
    protected $message = [ ];

    /**
     * @param string $json
     */
    public function __construct ( $json = '' )
    {
        $this->message = json_decode( $json, true );
    }

    /**
     * @return bool
     */
    public function isValid ()
    {
        return ( $this->isRegisterRequest() || $this->isStatusRequest() ) && isset( $this->message[ static::HASH ] );
    }

    /**
     * @return bool
     */
    public function isRegisterRequest ()
    {
        return isset( $this->message[ static::COMMAND_REGISTER ][ static::VALUE_ID ] );
    }

    /**
     * @return bool
     */
    public function isStatusRequest ()
    {
        return isset( $this->message[ static::COMMAND_STATUS ][ static::VALUE_ID ] );
    }

    /**
     * @return string|false
     */
    public function getHash ()
    {
        if ( $this->isValid() ) {
            return $this->message[ static::HASH ];
        }
        return false;
    }

    /**
     * @return int|false
     */
    public function getRegisterId ()
    {
        if ( $this->isRegisterRequest() ) {
            return (int) $this->message[ static::COMMAND_REGISTER ][ static::VALUE_ID ];
        }
        return false;
    }

    /**
     * @return string|false
     */
    public function getRegisterStatus ()
    {
        if ( $this->isRegisterRequest() ) {
            return $this->message[ static::COMMAND_REGISTER ][ static::VALUE_STATUS ];
        }
        return false;
    }

    /**
     * @return int|false
     */
    public function getStatusId ()
    {
        if ( $this->isStatusRequest() ) {
            return (int) $this->message[ static::COMMAND_STATUS ][ static::VALUE_ID ];
        }
        return false;
    }
}