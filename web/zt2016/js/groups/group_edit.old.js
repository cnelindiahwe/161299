$(function(){
	
	deletebutton();
	updategroup();


});   //$(function()  
 
function updategroup()
	{
		//##################  show modal on main form submit to prevent further action before completing the request ################## 

		$("#group-data-form").submit(function(e) {
			var dialog = bootbox.dialog({
			    message: '<p class="text-center">Updating group data ...</p><div class="progress">  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%"><span class="sr-only">45% Complete</span></div></div>',
			    closeButton: false
			});
				
			}
		);
	}          
 

// ##################  delete file button behavior  ################## 
 function deletebutton()
	{
		$('.btn-delete').click ( function () {
			delete_location =$(this).attr('href') 
			//ask for deletion confimation
			bootbox.confirm("Trash group?", function(result) {
			    if (result) {
			        $("#group-trash-form").submit();
			    } 
			});
		return false;
		});

		
	}
