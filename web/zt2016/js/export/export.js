$(function(){
	
	// check if "backupfileslist" div exists
	if($("#backupfileslist").length){
		
		// hide "backupfileslist" div
		$("#backupfileslist").hide();	// add show / hide tools button
		
		$( "#backupbuttons" ).append("<button type=\"button\" class=\"btn btn-default\" id=\"showdbbackupfiles\">Show auto backup files</button><button type=\"button\" class=\"btn btn-default\" style=\"display: none;\" id=\"hidedbbackupfiles\">Hide auto backup files</button>" );

		$('#showdbbackupfiles, #hidedbbackupfiles').click(function () {
			$("#backupfileslist, #showdbbackupfiles, #hidedbbackupfiles").toggle();
		})
	
	}	
		
});   //$(function()     