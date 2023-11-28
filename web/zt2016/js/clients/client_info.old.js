$(function(){
	
		//client dropdown
 		$('#client_dropdown_selector_submit').hide();
		$("#client_dropdown_selector").change ( function () {
				$("#client_dropdown_form").submit();
			}
		);



});   //$(function()     

