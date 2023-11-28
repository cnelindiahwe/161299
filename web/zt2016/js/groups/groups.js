$(function(){
		
 		$('#table_loading_message').hide() ;
	
	 	$('#groups_table')
			.css({ "display": "table", "width": "100%"})
			.dataTable({
				"dom":'<"col-sm-12"f>',
				"paging": false,
				"info": false,
			}); //$('#groups_table').dataTable

});   //$(function()     

