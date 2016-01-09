<script src="/js/StatusOnline.js"></script>
<script type="text/javascript">
    "use strict";

    var statusOnline = new StatusOnline();
    statusOnline.onReady( function () {
        var totalBox = document.getElementsByClassName( 'total' )[ 0 ];
        var currentBox = document.getElementsByClassName( 'current' )[ 0 ];
        var peakBox = document.getElementsByClassName( 'peak' )[ 0 ];
        var containerBox = document.getElementsByClassName( 'container' )[ 0 ];
        function showData( s ) {
            totalBox.innerHTML = s.count || '0';
            currentBox.innerHTML = s.memory.current || '?';
            peakBox.innerHTML = s.memory.peak || '?';
            if ( s.list !== undefined ) {
                var html = '';
                for ( var key in s.list ) {
                    if ( s.list.hasOwnProperty( key ) ) {
                        html += '<span>ID:{id} ~{connections}: {status}</span>'.replace( /{(.*?)}/gm, function ( n, string ) {
                            if ( string === 'id' ) {
                                return key;
                            } else if ( string === 'connections' ) {
                                return s.list[ key ].connections;
                            } else if ( string === 'status' ) {
                                return s.list[ key ].status;
                            }
                            return '?';
                        } );
                    }
                }
                containerBox.innerHTML = html;
            }
        }
        setInterval( function () {
            statusOnline.getTotalStatus( function ( s ) {
                showData( s );
            } );
        }, 1000 );
    } );
</script>
<style type="text/css">
    * {
        font-family: 'andale mono', 'lucida console', 'courier new', monospace;
        font-size: 12px;
    }
    .info {
        padding: 4px 8px;
    }
    .container span {
        display: inline-block;
        margin: 6px 0 0 6px;
        padding: 4px 8px 2px;
        border: 1px solid #5b94cc;
        border-radius: 8px;
        background: #b1c3d3;

    }
</style>
<div class="info">
    Total Users: <b><span class="total">?</span></b><br/>
    Memory current: <b><span class="current">?</span></b><br/>
    Memory peak: <b><span class="peak">?</span></b><br/>
</div>
<div class="container"></div>
