$(function(){
	
	
		//group dropdown
 		$('#group_dropdown_selector_submit').hide();
		$("#group_dropdown_selector").change ( function () {
				$("#groups_dropdown_form").submit();
			}
		);

 		$('#table_loading_message').hide() ;
 	
        $('#group_clients_table')
			.css({ "display": "table", "width": "100%"})
			.dataTable(); //$('#groups_table').dataTable

});   //$(function()  

// {
// 	"dom":'<"col-sm-12"f>',
// 	"paging": false,
// 	"info": false,
// }
           
 