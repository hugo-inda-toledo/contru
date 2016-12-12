$(document).ready(function() {
    var table = $('.maskTable').DataTable({
        buttons:        [  
            {
                extend: 'colvis',
                text: 'Seleccionar columnas',
                collectionLayout: 'fixed',
                columns: ':not(:first-child)',
                postfixButtons: [ 
                    {
                        extend: 'colvisRestore',
                        text: 'Restaurar todas',
                    }
                ]
            },{
                extend: 'print',
                key: {
                    key: 'p',
                    altkey: true
                },
                text: function ( dt, button, config ) {
                    return dt.i18n( 'buttons.print', 'Im<u>p</u>rimir' );
                }
            }
        ],
        stateSave: true,
        "dom": 'Bfrtip',
        scrollY:        "80vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedColumns:   {
            leftColumns: 1,
            rightColumns: 0
        },
        'searching' : true,
        "paging": false,
        //pageLength: 100,
        "ordering" :false,
        // orderable: false,
        // "deferRender": true, //sólo ajax
        fixedHeader: {
            header: true,
            footer: true
        },
        "processing": true,
        "columnDefs": [
            { "targets": [0, 5], 'searchable': true },
            { "targets": '_all', 'searchable': false },
            { width: "10%", targets: [0] },
            { "visible": true, targets: 5 },
            
        ],
        "language": { //http://datatables.net/reference/option/language
            'aria': {
                'sortAscending':    ': presionar la columna para orden ascendente',
                'sortDescending':   ': presionar la columna para orden descendente'
            },
            'emptyTable':     'No se encontraron resultados',
            'info':             'Mostrando _MAX_ registros',
            'infoEmpty':        'No hay registros',
            'infoFiltered':   '(Filtrados de _MAX_ resultados)',
            'infoPostFix':      '',
            'lengthMenu':       'Mostrando _MENU_ registros por página',
            // 'lengthMenu':     'Ver _MENU_ resultados',
            'loadingRecords':   'Cargando...',
            'paginate': {
                'first':    'Primera',
                'last':     'Última',
                'next':     'Siguiente',
                'previous': 'Anterior'
            },
            'processing':       'Procesando...',
            'search':           'Buscar:',
            'thousands':        '.',
            'zeroRecords':    'No se encontraron resultados',
        }
    });

    table.columns.adjust().draw();
});
