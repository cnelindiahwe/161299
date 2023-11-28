$(function(){

	// ##################  On load  ################## 


	 automateform();
	 deletebutton();
	 fileuploader();


// ##################  End $(function(){  ################## 
									  
});



// ################## activate client selector  ##################

 	function  automateform(){
		$("#clientcontrolsubmit").hide();
		$("#clientselector").change ( function () {
				$("#clientcontrol").submit();
			}
		);
	}
	


// ##################  delete file button behavior  ################## 
 function deletebutton()
	{
		$('.deletefile').click ( function () {
			delete_location =$(this).attr('href') 
			//ask for deletion confimation
			bootbox.confirm("Delete file?", function(result) {
			    if (result) {
			        document.location = delete_location;
			        //alert ((this).attr('href'));
			    } else {
			        //console.log("User declined dialog");
			    }
			});
			

		return false;
		});
			

	}


 //##################  manage  file upload form ################## 

function fileuploader(){

	$('#GroupUploadsubmit, #ClientUploadsubmit').remove();	
	$('#ClientUploadfileupload').change(function(){
		bootbox.confirm("Upload file?", function(result) {
		    if (result) {
		        $("#ClientUploadform").submit();
		    } 
		});
	});
	$('#GroupUploadfileupload').change(function(){
		bootbox.confirm("Upload file?", function(result) {
		    if (result) {
		        $("#GroupUploadform").submit();
		    } 
		});
	});
}
