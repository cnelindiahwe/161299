$(function(){
	
	//##### hide and automate client form submit
	changeclient();

	//##### hide and automate contact form submit
	changecontact();

	//##### number fields behavior 
	numbersfields();

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
			if (previousclient=="all"){
				 $('#client_dropdown_form').submit();
			} else{
				//ask for confimation
				bootbox.confirm("Change client?", function(result) {
					if (result) {
					  $('#client_dropdown_form').submit();
					}  else{
						$("#client_dropdown_selector").val(previousclient)	;
					}
				});			
			}
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

// ##### number fields behavior 
function numbersfields(){
	$('#NewSlides').change( function () { resetnumbersfields();	});
	$('#EditedSlides').change( function () { resetnumbersfields();	});
	$('#Hours').change( function () { resetnumbersfields();	});

	$('body').delegate('#NewSlides_2','change',function () { resetnumbersfields();	});
	$('body').delegate('#EditedSlides_2','change',function () { resetnumbersfields();	});
	$('body').delegate('#Hours_2','change',function () { resetnumbersfields();	});

	$('body').delegate('#NewSlides_3','change',function () { resetnumbersfields();	});
	$('body').delegate('#EditedSlides_3','change',function () { resetnumbersfields();	});
	$('body').delegate('#Hours_3','change',function () { resetnumbersfields();	});

}

// ################## reset number fields  ################## 
 function resetnumbersfields()
	{
		if ($('#NewSlides').val()==0) $('#NewSlides').val(0);
		if ($('#EditedSlides').val()==0) $('#EditedSlides').val(0);
		if ($('#Hours').val()==0) $('#Hours').val(0);
		if ($('#NewSlides_2').val()==0) $('#NewSlides_2').val(0);
		if ($('#EditedSlides_2').val()==0) $('#EditedSlides_2').val(0);
		if ($('#Hours_2').val()==0) $('#Hours_2').val(0);

		if ($('#NewSlides_3').val()==0) $('#NewSlides_3').val(0);
		if ($('#EditedSlides_3').val()==0) $('#EditedSlides_3').val(0);
		if ($('#Hours_3').val()==0) $('#Hours_3').val(0);

	}								
								
// ##################   job details form submit behavior  ################## 
 function jobdetailsformsubmit()
	{
		$('#job-details-form').submit ( function () {
			if ($('#NewSlides').val()==0 &&
			$('#EditedSlides').val()==0 &&
			$('#Hours').val()==0 )
			{				
				//ask for confimation to save voide job
				bootbox.confirm("Create job with 0 New, 0 Edits and 0 Hours?", function(result) {
					if (result) {
						$("#job-details-form").unbind().submit();
					}
				});
			}
			else
			{
				//ask for deletion confimation
				bootbox.confirm("Create job?", function(result) {
					if (result) {
						$("#job-details-form").unbind().submit();
					} 
				});
			}
			
			return false;
		
		});

	}












