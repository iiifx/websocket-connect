"use strict";

// Создаем клиент
var statusOnline = new StatusOnline( null, 8081 );

// Действия делаем только после того, как клиент будет готов к работе
statusOnline.onReady( function () {

    // Бекенд должен передать ID пользователя
    if ( userId !== undefined ) {

        // Проверяем статус этого ID
        statusOnline.getStatus( userId, function ( status ) {

            // Результатом будет false
            // Если, конечно, он не зарегистрировался с другой страницы

            //console.log( 'Status: ' + status );
        } );

        // Регистрируем пользователя
        statusOnline.register( userId );
        // Сейчас пользователь зарегистрировался с дефолтным статусом "О"
        // При регистрации можно передавать какой-то особый статус вторым параметром

        // Снова проверяем статус этого ID
        statusOnline.getStatus( userId, function ( status ) {

            // Сейчас результатом будет "О"

            //console.log( 'Status: ' + status );
        } );

        // Так же можно проверить и любого другого пользователя
        statusOnline.getStatus( 4747, function ( status ) {
            //console.log( 'Status: ' + status );
        } );

        // При закрытии страницы соединение WS разрывается и регистрация этого пользователя отменяется

        // @TODO Надо еще сделать возможность смены статуса после регистрации

    }
} );
