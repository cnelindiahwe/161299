$(function(){
	
		//client dropdown
 		$('#client_dropdown_selector_submit').hide();
		$("#client_dropdown_selector").change ( function () {
				$("#client_dropdown_form").submit();
			}
		);

		//contact dropdown
 		$('#contact_dropdown_selector_submit').hide();
		$("#contact_dropdown_selector").change ( function () {
				$("#contact_dropdown_form").submit();
			}
		);

});   //$(function()     

