$(function(){

	// ##################  On load  ################## 


	 automateform();
	 fileuploader();
	 deletebutton();


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
			//ask for deletion confimation
			var answer = confirm("Delete file?")
			if (answer){
				document.location = (this).attr('href');
			}
		return false;
		});
			

	}


// ##################  manage  file upload form ################## 
	
	function fileuploader()
	{
		
			$('#uploadform').submit(function(){
				//do nothing if name field is blank
				var fileforupload= $("#fileuploadname").val();
				fileforupload= $.trim(fileforupload);
				if (fileforupload !="") {
					var myArray = fileforupload.split("\\");
					fileforupload = myArray[myArray.length-1];
					fileforupload = fileforupload.replace('.', '_');
					fileforupload= "." + fileforupload;
						
					
					if ($(fileforupload).length==0) {
						//$('#uploadform').submit();
						alert(fileforupload);
					} else 
					{
						alert("A file with the same name exists in the current directory.\n Please delete it before uploading abother file with the same name.");	
					}
				}
				else
				{
					alert("No file selected.");	
				}				
				return false;
			});
	
	}	