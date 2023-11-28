$(function(){
	
	// hide edit tools
		$("#createdirform, #uploadform, .deletedir, .deletefile").hide();


	// add show / hide tools button
		$('.panel-heading h4')
			.prepend( "<button type=\"button\" class=\"btn btn-default btn-xs pull-right\" id=\"showtoolsbutton\">More</button><button type=\"button\" class=\"btn btn-default btn-xs pull-right\" style=\"display: none;\" id=\"hidetoolsbutton\">Less</button>");

		$('#showtoolsbutton, #hidetoolsbutton').click(function () {
			$("#showtoolsbutton, #hidetoolsbutton, .deletedir, .deletefile").toggle();
			$("#createdirform, #uploadform").slideToggle();
		})
	
	// display upload file modal
		$('#uploadform').submit(function(e) {

			//e.preventDefault();

			//var currentForm = this;
			showprogress();

			//currentForm.submit();
			
		});	
	
	
	// create directory submit confirmation 
	// via bootbox
		$('#createdirform').submit(function(e) {
			e.preventDefault();

			var currentForm = this;
			var newdir = $('#createdirname').val();
			if ($('#currentdir').val()!="") {
				newdir = $('#currentdir').val() + '/'+newdir ;
			}
			
			var msg= "Create directory Limbo/" + newdir + "?";	

			bootbox.confirm(msg, function(result) {
				if (result) {
					currentForm.submit();
				} 
			}); //bootbox.confirm	    
			
		});	
	
	// delete directory submit confirmation 
	// via bootbox
		$('.deletedir').click(function(e) {
			
			e.preventDefault();
			// Pick name by selecting what comes after "zt2016_deletelimbodir/"
			// in the url of the clicked href 
			// 22 being the character count of "zt2016_deletelimbodir"
			
			var hrefurl=$(this).attr('href');
			var deletedirname = hrefurl.substring(hrefurl.lastIndexOf("zt2016_deletelimbodir/") + 22);
			
			var msg= "Delete directory Limbo/" + deletedirname + "?";	

			bootbox.confirm(msg, function(result) {
				if (result) {
					window.location = hrefurl;
				} 
			}); //bootbox.confirm
			
			
		});		
	
	// delete file submit confirmation 
	// via bootbox
		$('.deletefile').click(function(e) {
			
			e.preventDefault();
			// Pick name by selecting what comes after "zt2016_deletelimbodir/"
			// in the url of the clicked href 
			// 22 being the character count of "zt2016_deletelimbodir"
			
			var hrefurl=$(this).attr('href');
			var deletedirname = hrefurl.substring(hrefurl.lastIndexOf("zt2016_deletelimbofile/") + 23);
			
			var msg= "Delete file Limbo/" + deletedirname + "?";	

			bootbox.confirm(msg, function(result) {
				if (result) {
					window.location = hrefurl;
				} 
			}); //bootbox.confirm
			
			
		});	
		
		
});   //$(function()     

function showprogress() {
	$('body').append('<!-- Modal -->'+
		'<div class="modal fade" id="fileuploadprogressbar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
		'  <div class="modal-dialog modal-sm">' +
		'	<div class="modal-content">' +
		'	  <div class="modal-header">' +
		'		  <h5 style="text-align:center !important">Uploading file. Please wait until the page refreshes.</h5>' +
		'	  </div>' +
		'	  <div class="modal-body">' +
		'		<div style="margin:auto; background-color:#fff; border: 16px solid #f3f3f3; border-radius: 50%; border-top: 16px solid #3498db;  width: 120px; height: 120px; -webkit-animation: spin 2s linear infinite; /* Safari */ animation: spin 2s linear infinite;">' +
		'		</div>' +
		'	  </div>' +
		'	</div>' +
		'  </div>' +
		'</div>');
		$("#fileuploadprogressbar").modal('show');
			
}
