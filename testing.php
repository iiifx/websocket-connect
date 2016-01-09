<?php

$userId = isset( $_GET[ 'id' ] ) ? (int) $_GET[ 'id' ] : 0;

?>

<script type="text/javascript">
    var userId = <?= $userId; ?>;
</script>
<script src="/js/StatusOnline.js"></script>
<script type="text/javascript">
    "use strict";

    var perSecondConnections = 0;
    var perSecondRequests = 0;

    setInterval( function () {
        document.body.innerHTML = 'Per second: <br> -- Connections: ' + perSecondConnections + '<br> -- Requests: ' + perSecondRequests;
        //perSecondConnections = 0;
        perSecondRequests = 0;
    }, 1000 );

    function rand( min, max ) {
        return Math.floor( Math.random() * (max - min + 1) ) + min;
    }

    function simulateS( interval ) {
        setInterval( function () {
            var so = new StatusOnline();
            so.onReady( function () {
                perSecondConnections++;
                simulateR( so );
            } );
        }, interval * 1000 );
    }

    function simulateR ( so ) {
        var id = rand( 1, 1000000000 );
        so.registerClient( id, rand( 1, 3 ) );
        perSecondRequests++;
		
		setTimeout( function () {
			if ( rand( 1, 100 ) > 10 ) {
				so.getStatus( id );
				perSecondRequests++;
			}
		}, 1000 );
		
		setTimeout( function () {
			if ( rand( 1, 100 ) > 30 ) {
				so.changeStatus( rand( 1, 3 ) );
				perSecondRequests++;
			}
		}, 2000 );
        
		setTimeout( function () {
			if ( rand( 1, 100 ) > 10 ) {
				so.getStatus( id, function () {} );
				perSecondRequests++;
			}
		}, 3000 );
		
		setTimeout( function () {
			if ( rand( 1, 100 ) > 30 ) {
				so.changeStatus( rand( 1, 3 ) );
				perSecondRequests++;
			}
		}, 4000 );

        setTimeout( function () {
		    perSecondConnections--;
            so.close();
        }, rand( 5, 9 ) * 1000 );
    }

	var countConn = 200;
	var timeoutConn = 100;
	
	for ( var i = 0; i <= countConn; i ++ ) {
		setTimeout( function () {
			simulateS( rand( 10, 30 ) );
		}, timeoutConn * ( i + 1 ) );
	}

</script>
