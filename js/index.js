"use strict";

// ������� ������
var statusOnline = new StatusOnline( null, 8081 );

// �������� ������ ������ ����� ����, ��� ������ ����� ����� � ������
statusOnline.onReady( function () {

    // ������ ������ �������� ID ������������
    if ( userId !== undefined ) {

        // ��������� ������ ����� ID
        statusOnline.getStatus( userId, function ( status ) {

            // ����������� ����� false
            // ����, �������, �� �� ����������������� � ������ ��������

            //console.log( 'Status: ' + status );
        } );

        // ������������ ������������
        statusOnline.register( userId );
        // ������ ������������ ����������������� � ��������� �������� "�"
        // ��� ����������� ����� ���������� �����-�� ������ ������ ������ ����������

        // ����� ��������� ������ ����� ID
        statusOnline.getStatus( userId, function ( status ) {

            // ������ ����������� ����� "�"

            //console.log( 'Status: ' + status );
        } );

        // ��� �� ����� ��������� � ������ ������� ������������
        statusOnline.getStatus( 4747, function ( status ) {
            //console.log( 'Status: ' + status );
        } );

        // ��� �������� �������� ���������� WS ����������� � ����������� ����� ������������ ����������

        // @TODO ���� ��� ������� ����������� ����� ������� ����� �����������

    }
} );
