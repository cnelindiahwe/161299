	

$(function(){
	VATactive = 0;
	interactive_VAT_checkbox();
	adjust_interactive_VAT_before_submission();
	confirm_deletion_existing_URL();


});   //$(function()   


// ##################  change amount when VAT checkbox is ticked  ################## 
 function interactive_VAT_checkbox()
	{
	 	$('#VATCheck').change(function() {
	           
	         formtotal =  $('#InvoiceTotal').val();
	 		var newtotal = parseFloat(formtotal).toFixed(2);
	 			
	        if($(this).is(":checked")) {
	            var vattotal = newtotal*.21
	            newtotal =+newtotal + +vattotal;
	       } else{
	            
	            newtotal =+newtotal/1.21;
	        	
	       }
	        newtotal =newtotal.toFixed(2);
	        $('#InvoiceTotal').val(newtotal);  
	        //$('#textbox1').val($(this).is(':checked'));        

	    });
 	}

// ##################  request confirmation on deleting existing ogone form  ################## 
 function confirm_deletion_existing_URL()
	{
		$('#postogoneform').submit ( function (e) {
			 
	 		e.preventDefault();
	 		//check if existing payment callout exists
	 		var currentForm = this;
	 		if ($(".bs-callout").length) {
	 			 
				//ask for deletion confimation
				bootbox.confirm("Delete exisiting payment URL?", function(result) {
				    if (result) {
				        currentForm.submit();
				    } 
				});
			} else {
				currentForm.submit();
			 	//return;
			}
			return false;
		});
	} 


 
// ##################  request confirmation on deleting existing ogone form when submitting new one ################## 
 function confirm_deletion_existing_URL()
	{

		// ##################  request confirmation on deleting existing ogone form when submitting new one ################## 
		$('#postogoneform').submit ( function (e) {
			 
	 		e.preventDefault();
	 		//check if existing payment callout exists
	 		var currentForm = this;
	 		if ($(".bs-callout").length) {
	 			 
				//ask for deletion confimation
				bootbox.confirm("Delete exisiting payment URL?", function(result) {
				    if (result) {
				        currentForm.submit();
				    } 
				});
			} else {
				currentForm.submit();
			 	//return;
			}
			return false;
		});

		// ##################  request confirmation on deleting existing ogone form when clicking delete button ################## 
		$('#ogonedeleteform').submit ( function (e) {
			 
	 		e.preventDefault();
	 		//check if existing payment callout exists
	 		var currentForm = this;
			//ask for deletion confimation
			bootbox.confirm("Delete exisiting payment URL?", function(result) {
			    if (result) {
			        currentForm.submit();
			    } 
			});
			return false;
		});

	} 
 
// ##################  updates value before submitting if interactive VAT checkbox has been used  ################## 
 function adjust_interactive_VAT_before_submission()
	{
		$('#ogoneform').submit ( function (e) {
			
	 			if($('#VATCheck').is(":checked")) {
	 				e.preventDefault();
	 				var currentForm = this
					 adjusttotal()
					currentForm.submit()
				}
		});
	} 
	
	
function adjusttotal() {

		formtotal= $('#InvoiceTotal').val();
		var newtotal = parseFloat(formtotal).toFixed(2);
		newtotal= newtotal / 1.21;
		$('#InvoiceTotal').val(newtotal);		

}
