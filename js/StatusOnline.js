"use strict";

/**
 *
 *
 * @param {string=} host
 * @param {int=} port
 * @param {boolean=}secure
 *
 * @constructor
 */
function StatusOnline( host, port, secure ) {
    // Статусы
    this.STATUS_ONLINE = 'O';
    this.DEFAULT_STATUS = this.STATUS_ONLINE;
    // Команды
    this.HASH = 'h';
    this.COMMAND_REGISTER = 'register';
    this.COMMAND_STATUS = 'status';
    this.VALUE_ID = 'id';
    this.VALUE_STATUS = 'status';
    // Параметры и соединение
    this.method = secure ? 'wss' : 'ws';
    this.host = host || window.location.host;
    this.port = port || 8080;
    // Обработчики сообщений
    this.messageCallbacks = {};
}
/**
 *
 *
 * @param {function=} callback
 */
StatusOnline.prototype.onReady = function ( callback ) {
    if ( !this.connection ) {
        var so = this;
        try {
            this.connection = new WebSocket( this.method + '://' + this.host + ':' + this.port );
            if ( typeof callback === 'function' ) {
                this.connection.onopen = callback;
            }
            this.connection.onmessage = function ( e ) {
                //noinspection JSAccessibilityCheck
                so._onMessage( so, e );
            }
        } catch ( e ) {
            // @TODO
        }
    }
};
/**
 *
 *
 * @param {int} userId
 * @param {string=} status
 */
StatusOnline.prototype.register = function ( userId, status ) {
    var hash = new Date().getTime().toString() + Math.random().toString().replace( '.', '' );
    var message = {};
    message[ this.HASH ] = hash;
    message[ this.COMMAND_REGISTER ] = {};
    message[ this.COMMAND_REGISTER ][ this.VALUE_ID ] = userId;
    message[ this.COMMAND_REGISTER ][ this.VALUE_STATUS ] = status || this.DEFAULT_STATUS;
    this.connection.send( JSON.stringify( message ) );
};
/**
 *
 *
 * @param {int} userId
 * @param {function} callback
 */
StatusOnline.prototype.getStatus = function ( userId, callback ) {
    if ( userId > 0 ) {
        var hash = new Date().getTime().toString() + Math.random().toString().replace( '.', '' );
        var message = {};
        message[ this.HASH ] = hash;
        message[ this.COMMAND_STATUS ] = {};
        message[ this.COMMAND_STATUS ][ this.VALUE_ID ] = userId;
        this.messageCallbacks[ hash ] = function ( message ) {
            if ( typeof callback === 'function' ) {
                if ( message.status !== undefined ) {
                    callback( message.status );
                }
            }
        };
        this.connection.send( JSON.stringify( message ) );
    }
};
/**
 *
 *
 * @param {StatusOnline} o
 * @param {object} e
 *
 * @private
 */
StatusOnline.prototype._onMessage = function ( o, e ) {
    console.log( 'Message [' );
    console.log( '    -- Message ' + e.data );
    var message = JSON.parse( e.data );
    if ( message[ o.HASH ] !== undefined ) {
        var h = message[ o.HASH ];
        console.log( '    -- Hash Found: ' + h );
        if ( typeof this.messageCallbacks[ h ] === 'function' ) {
            this.messageCallbacks[ h ]( message );
            delete this.messageCallbacks[ h ];
            console.log( '    -- Callback Executed' );
        }
    }
    console.log( ']' );
};
