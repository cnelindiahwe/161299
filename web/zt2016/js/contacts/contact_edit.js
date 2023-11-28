$(function(){

	//##################  client dropdown  ################## 
	$('#client_dropdown_selector_submit').hide();

	$("#client_dropdown_selector").change ( function () {
			$("#client_dropdown_form").submit();
		}
	);

	//##################  contact dropdown  ################## 
	$('#contact_dropdown_selector_submit').hide();
	$("#contact_dropdown_selector").change ( function () {
			$("#contact_dropdown_form").submit();
		}
	);	
	
	


	//##################  HomeCountry dropdown  ################## 
	//Copy value to officeCountry dropdown if empty
	$("#HomeCountry").change ( function () {
			if (!$('#OfficeCountry').find(":selected").text()){
				//alert ("Hooha!" +$("#HomeCountry").value );
				$("#OfficeCountry  option[value='"+this.value+"']").prop('selected', true) ;	
			}
		}
	);

	// ##################  delete file button behavior  ################## 
	deletebutton();

	

});   //$(function()     

// ##################  delete file button behavior  ################## 
 function deletebutton()
	{
		$('.btn-delete').click ( function () {
			delete_location =$(this).attr('href') 
			//ask for deletion confimation
			bootbox.confirm("Trash contact?", function(result) {
			    if (result) {
			        document.location = delete_location;
			    } 
			});
		return false;
		});

	}	