$(function(){
		//$('.datepicker').datepicker()
		
		
	
		$.fn.dataTable.moment( 'D-MMM-YYYY' );
		$('#invoices_table')
			.DataTable( {
			"dom":' <"col-sm-6"f><"col-sm-6"i>t<"col-sm-6"p><"col-sm-6"l>r<"clear">',
			//"dom":'difrtp',
			"order": [[ 2, "desc" ]],
			"scrollY":        "22em",
			"scrollCollapse": true,
			//"paging":         false,
			"info":           true,
			//"processing": true,
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
			"ajax": "../zt2016_existing_invoices_ajax",
 			
			"initComplete": function () {
				$('#table_loading_message').hide() ;
				$('#invoices_table_wrapper').css({ "display": "block"});
				$('#invoices_table').css({ "display": "table", "width": "100%"})
			}			
		
		} );
		


});   //$(function()     

