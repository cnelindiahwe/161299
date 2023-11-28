$(function(){
	
		//client dropdown
 		$('#client_dropdown_selector_submit').hide();
		$("#client_dropdown_selector").change ( function () {
				$("#client_dropdown_form").submit();
			}
		);

        $('.contacts_table')
			.css({ "display": "table", "width": "100%"})
			.dataTable({
				"paging":		false,
				"info":			false,
				"searching": false
				//"searching":	false,
			});

});   //$(function()     

