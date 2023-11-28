$(function(){
	
		//capture member's time zone
		var mytimezone= $( "#update_TimeZone_dropdown option:selected" ).val();
        
        //fade out alerts
        $(".error_msg").fadeOut(5000);
        
        //hide form submit button
        $( "#timezone_change" ).hide();
        
        //show form submit button on dropdown selection change
        $( "#update_TimeZone_dropdown" ).change(function() {
        		$( "#timezone_change" ).show();
        });
        
        
        //from submit button confirmation
        $( "#timezone_change" ).click(function() {
        	

			BootstrapDialog.show({
				type: BootstrapDialog.TYPE_DANGER,
				title: 'Confirm Time Zone change',
				message: 'Changing a time zone is a major move. Do you still want to go ahead?',
				buttons: [{
					label: 'Confirm',
					cssClass: 'btn-primary',
					action: function(dialog){
						var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
	                    $button.disable();
	                    $button.spin();
    					$( ".form-timezone" ).submit();
					}
				}, {
					label: 'Cancel',
					action: function(dialogRef){
						$( "#update_TimeZone_dropdown" ).val(mytimezone);
						$( "#timezone_change" ).hide();
						dialogRef.close();
					}
				}]
			}); 
			return false;      
		}); 

});        
