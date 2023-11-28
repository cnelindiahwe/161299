$(function(){
    //$('.datepicker').datepicker()
    $('#table_loading_message').hide() ;
    

    $.fn.dataTable.moment( 'D-MMM-YYYY' );

    $('#invoices_table').css({ "display": "table", "width": "100%"}).dataTable( {
    "dom":' <"col-sm-6"f><"col-sm-6"i>t<"bottom10"p><"bottom20"l>r<"clear">',
    //"dom":'difrtp',
    "order": [[ 2, "desc" ]],
    "scrollY":        "20em",
    "scrollCollapse": true,
    "paging":         false,
    "info":           true,
    // "processing": true,
    "orderClasses": false,
    "lengthMenu": [[10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "All"]],
    columnDefs: [ {
        targets: [ 3 ],
        orderData: [ 0, 1 ]
    }, {
        targets: [ 1 ],
        orderData: [ 1, 0 ]
    } ],
    "processing": true,
	"deferRender": true,


    
    
}); //$('#invoices_table').dataTable

});   //$(function()     
