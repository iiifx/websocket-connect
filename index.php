<?php

$userId = isset( $_GET[ 'id' ] ) ? (int) $_GET[ 'id' ] : 0;

?>

<script type="text/javascript">
    var userId = <?= $userId; ?>;
</script>
<script src="/js/StatusOnline.js"></script>
<script type="text/javascript">
    "use strict";

    // Создаем клиент
    var statusOnline = new StatusOnline();
    // Действия делаем только после того, как клиент будет готов к работе
    statusOnline.onReady( function () {

        // Бекенд должен передать ID пользователя
        //if ( userId !== undefined ) {

        setInterval( function () {
            var id =
        }, 1000 );
        setInterval( function () {

        }, 5000 );
        setInterval( function () {

        }, 10000 );
        setInterval( function () {

        }, 30000 );


            statusOnline.registerClient( 1 );
            statusOnline.changeStatus( 2 );
            statusOnline.registerClient( 2 );
            statusOnline.changeStatus( 3 );
            statusOnline.registerClient( 3, 2 );
            statusOnline.getStatus( 3, function ( s ) {
                console.log( s );
            } );
            statusOnline.changeStatus( 4 );
            statusOnline.getStatus( 3, function ( s ) {
                console.log( s );
            } );

            statusOnline.getTotalStatus( function ( ts ) {
                console.log( ts );
            } );


            // Проверяем статус этого ID
            //statusOnline.getStatus( userId, function ( status ) {
                // Результатом будет false
                // Если, конечно, он не зарегистрировался с другой страницы
                //console.log( 'Status: ' + status );
            //} );

            // Регистрируем пользователя
            //statusOnline.registerClient( userId );
            // Сейчас пользователь зарегистрировался с дефолтным статусом "О"
            // При регистрации можно передавать какой-то особый статус вторым параметром

            // Снова проверяем статус этого ID
            //statusOnline.getStatus( userId, function ( status ) {
                // Сейчас результатом будет "О"
                //console.log( 'Status: ' + status );
            //} );

            // Так же можно проверить и любого другого пользователя
            //statusOnline.getStatus( 4747, function ( status ) {
                //console.log( 'Status: ' + status );
            //} );

            // При закрытии страницы соединение WS разрывается и регистрация этого пользователя отменяется

        //}
    } );
</script>
