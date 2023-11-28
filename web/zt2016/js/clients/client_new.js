$(function(){
		
		//client code to uppercase
		$("#ClientCode").change ( function () {
				this.value = this.value.toUpperCase();
			}
		);

	
		//group change leads to reloadsing of page with specific group settings
		//https://stackoverflow.com/questions/2054705/non-ajax-jquery-post-request
		$("#Group").change ( function () {
			
			//alert( this.value);
			
			
			
			var updategroupform = $('<form action="zt2016_client_new" method="POST">' + 
			'<input type="hidden" name="ClientFormValues[GroupName]" value="' + this.value + '">' +
			'</form>');
			$(document.body).append(updategroupform);
			updategroupform.submit();
			
			
				//window.location.href = "file2.html/"&this.value;
			}
		);	
	
		//show modal on main form submit

		$("#client-information-form").submit(function(e) {
			//var currentForm = this;
	        //e.preventDefault();
			//alert('Heyyey');
			var dialog = bootbox.dialog({
			    message: '<p class="text-center">Please wait while the client is created ...</p><div class="progress">  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%"><span class="sr-only">45% Complete</span></div></div>',
			    closeButton: false
			});
				
			}
		);
		

});   //$(function()     

