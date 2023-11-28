$(function() {

		// Loading message
		$('.table_loading_message').hide() ;

		// Datatable	
		$('.table-results').css({ "display": "table", "width": "100%"})
        
		// delete  button 
		deletebutton();

});   //$(function()     



// ##################  delete client button behavior  ################## 
 function deletebutton()
	{
		$('.btn-delete').click ( function () {
			var delete_location =$(this).attr('href') 
			var delete_item =$(this).attr('id').replace("Delete", "");
			delete_item =delete_item.replace("Job", "Job #");
			//ask for deletion confimation
			bootbox.confirm("Permanently delete " + delete_item + "?", function(result) {
				if (result) {
					document.location = delete_location;
					bootbox.dialog({
						message: '<p class="text-center">Deleting ' + delete_item + '</p><div class="progress">  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%"><span class="sr-only">45% Complete</span></div></div>',
						//closeButton: false
					});
				} 
			});
		return false;
		});

	}