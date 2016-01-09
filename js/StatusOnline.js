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
    this.STATUS_ONLINE = 1;
    this.DEFAULT_STATUS = this.STATUS_ONLINE;
    this.REQUEST_ID = '_rid';
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
 * @param {function} callback
 */
StatusOnline.prototype.getTotalStatus = function ( callback ) {
    var hash = new Date().getTime().toString() + Math.random().toString().replace( '.', '' );
    var message = {};
    message[ this.REQUEST_ID ] = hash;
    message[ 'total-status' ] = true;
    this.messageCallbacks[ hash ] = function ( message ) {
        if ( typeof callback === 'function' ) {
            if ( message[ 'total-status' ] !== undefined ) {
                callback( message[ 'total-status' ] );
            }
        }
    };
    this.connection.send( JSON.stringify( message ) );
};
/**
 *
 *
 * @param {int} userId
 * @param {function=} callback
 */
StatusOnline.prototype.getStatus = function ( userId, callback ) {
    var hash = new Date().getTime().toString() + Math.random().toString().replace( '.', '' );
    var message = {};
    message[ this.REQUEST_ID ] = hash;
    message[ 'get-status' ] = {};
    message[ 'get-status' ][ 'id' ] = userId;
    this.messageCallbacks[ hash ] = function ( message ) {
        if ( typeof callback === 'function' ) {
            if ( message.status !== undefined ) {
                callback( message.status );
            }
        }
    };
    this.connection.send( JSON.stringify( message ) );
};
/**
 *
 *
 * @param {string|int} status
 */
StatusOnline.prototype.changeStatus = function ( status ) {
    var hash = new Date().getTime().toString() + Math.random().toString().replace( '.', '' );
    var message = {};
    message[ this.REQUEST_ID ] = hash;
    message[ 'change-status' ] = {};
    message[ 'change-status' ][ 'status' ] = status;
    this.connection.send( JSON.stringify( message ) );
};
/**
 *
 *
 * @param {int} id
 * @param {string|int=} status
 */
StatusOnline.prototype.registerClient = function ( id, status ) {
    var rid = new Date().getTime().toString() + Math.random().toString().replace( '.', '' );
    var message = {};
    message[ this.REQUEST_ID ] = rid;
    message[ 'register' ] = {};
    message[ 'register' ][ 'id' ] = id;
    message[ 'register' ][ 'status' ] = status || this.DEFAULT_STATUS;
    this.connection.send( JSON.stringify( message ) );
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
    var message = JSON.parse( e.data );
    if ( message[ o.REQUEST_ID ] !== undefined ) {
        var rid = message[ o.REQUEST_ID ];
        if ( typeof this.messageCallbacks[ rid ] === 'function' ) {
            this.messageCallbacks[ rid ]( message );
            delete this.messageCallbacks[ rid ];
        }
    }
};
/**
 *
 */
StatusOnline.prototype.close = function () {
    if ( this.connection ) {
        this.connection.close();
    }
};
