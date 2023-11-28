	

$(function(){
	VATactive = 0;
	interactive_VAT_checkbox();
	adjust_interactive_VAT_before_submission();
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
	
// ##################  Sets total to 2 decimal places  ################## 	
function adjusttotal() {

		formtotal= $('#InvoiceTotal').val();
		var newtotal = parseFloat(formtotal).toFixed(2);
		newtotal= newtotal / 1.21;
		$('#InvoiceTotal').val(newtotal);		

}


