$(function(){
	
	//##### automate client form submit
	changeclient();

	//#####  automate contact form submit
	changecontact();

	//##### delete job button behavior  
	trashbutton();	

	//##### cancel edit button behavior  
	cancelbutton();		
	
	//##### duplicate button behavior  
	duplicatebutton();
	
	//##### job details form submit behavior
	jobdetailsformsubmit();	
	
});      

// ##################   automate client form submit ################## 
 function changeclient()
	{
		$('#client_dropdown_selector_submit').remove();
		
		$('#client_dropdown_selector').on('focus', function () {

			// Store the current value on focus and on change
			previousclient = this.value;
			
		}).change ( function () {

			//ask for confimation
			bootbox.confirm("Change client?", function(result) {
			    if (result) {
			      $('#client_dropdown_form').submit();
			    }  else{
					$("#client_dropdown_selector").val(previousclient)	;
				}
			});

		});

		
	}	
// ##################   automate contactform submit ################## 
 function changecontact()
	{
		$('#contact_dropdown_selector_submit').remove();
   
	//https://stackoverflow.com/questions/4076770/getting-value-of-select-dropdown-before-change	
	var previous;

   $("#contact_dropdown_selector").on('focus', function () {
        // Store the current value on focus and on change
        previous = this.value;
    }).change(function() {
		//ask for confimation if previous value was not empty
		if (previous==""){
			$("#contact_dropdown_form").submit();
		} else {
			bootbox.confirm("Change originator?", function(result) {
				if (result) {
				  $("#contact_dropdown_form").submit();
				} else{
					$("#contact_dropdown_selector").val(previous)	;
				}
			});
		} 

    });
	
	
	}	


// ##################  delete file button behavior  ################## 
 function trashbutton()
	{
		$('.trash-button').click ( function () {
			delete_location =$(this).attr('href'); 
			//ask for deletion confimation
			bootbox.confirm("Trash job?", function(result) {
			    if (result) {
					document.location = delete_location;
			    } 
			});
		return false;
		});

	}	


// ##################  cancel file button behavior  ################## 
 function cancelbutton()
	{
		$('.cancel-button').click ( function () {
			cancel_location =$(this).attr('href'); 
			//ask for deletion confimation
			bootbox.confirm("Cancel edit?", function(result) {
			    if (result) {
					document.location = cancel_location;
			    } 
			});
		return false;
		});

	}	

// ##################  duplicate file button behavior  ################## 
 function duplicatebutton()
	{
		$('.duplicate-button').click ( function () {
			//ask for deletion confimation
			bootbox.confirm("Duplicate Job?", function(result) {
			    if (result) {
					 $("#duplicate-job-form").submit();
			    } 
			});
		return false;
		});

	}	

// ##################   job details form submit behavior  ################## 
 function jobdetailsformsubmit()
	{
		$('#JobUpdateSubmit').click ( function () {
			if ($('#NewSlides').val()==0 &&
			$('#EditedSlides').val()==0 &&
			$('#Hours').val()==0 )
			{				
				//ask for confimation to save void job
				bootbox.confirm("Save job with 0 New, 0 Edits and 0 Hours?", function(result) {
					if (result) {
						$("#job-details-form").submit();
					}
				});
			}
			else
			{
				//ask for deletion confimation
				bootbox.confirm("Update job details?", function(result) {
					if (result) {
						$("#job-details-form").submit();
					} 
				});
			}
			
			return false;
		
		});

	}


