$(function(){

	// ##################  On load  ################## 

		deletebuttons();
		newdirectory();
		fileuploader();

	
// ##################  End $(function(){  ################## 
});
// ##################  delete file buttons behavior  ################## 
function deletebuttons()
	{
		$('.deletefile').click ( function () {
			var answer = confirm("Delete file?")
			if (answer){
				document.location = $(this).attr('href');
			}
		return false;
		});
			

		$('.deletedir').click ( function () {
			var answer = confirm("Delete directory?")
			if (answer){
				document.location = $(this).attr('href');
			}
		return false;
		});
			
	}
// ##################  Catch duplicate folder names upon folder creation{  ################## 

	
function newdirectory()
	{
		
			$('#createdirform').submit(function(){
				//do nothing if name field is blank
				if ($.trim($("#createdirname").val())!="") {
					//Alerts if directory exists
					urlform=$("#createdirform").attr("action");
					formdata=$("#createdirform").serialize();
					$.ajax({
					 type:"POST",
					 url: urlform,
					 data:formdata,
					 success: function(data) {
					 	if (data.slice(0,9) == "Directory") {
					    	alert(data);
					  	} else {
					  		document.location.reload(true);
					  	}
					  }
					});
				}
				else
				{
					alert("Please fill in name of new directory first");	
				}				
				return false;
			});
	
	}
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
						$('#uploadform').submit();
					} else 
					{
						alert("A file with the same name exists in the current directory.\n Please delete it before uploading abother file with the same name.");	
					}
				}
				else
				{
					alert("Please select a file first");	
				}				
				return false;
			});
	
	}