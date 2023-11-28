$(function(){
	
		//##################  client dropdown  ################## 
 		$('#client_dropdown_selector_submit').hide();
 		
		$("#client_dropdown_selector").change ( function () {
				$("#client_dropdown_form").submit();
			}
		);
		
		
		//##################  client code to uppercase  ################## 
		$("#ClientCode").change ( function () {
				this.value = this.value.toUpperCase();
			}
		);
		
		//##################  show modal on main form submit to prevent further action before completing the request ################## 

		$("#client-information-form").submit(function(e) {
			var dialog = bootbox.dialog({
			    message: '<p class="text-center">Updating client data ...</p><div class="progress">  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%"><span class="sr-only">45% Complete</span></div></div>',
			    closeButton: false
			});
				
			}
		);

		//##################  show modal on client selector form submit to prevent further action before completing the request ################## 

		$("#client_dropdown_form").submit(function(e) {
			var dialog = bootbox.dialog({
			    message: '<p class="text-center">Loading client data ...</p><div class="progress">  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%"><span class="sr-only">45% Complete</span></div></div>',
			    closeButton: false
			});
				
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
			bootbox.confirm("Trash client?", function(result) {
			    if (result) {
			        document.location = delete_location;
			    } 
			});
		return false;
		});

	}