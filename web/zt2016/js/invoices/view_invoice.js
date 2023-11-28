$(function(){
	
		// capture status and date values
		var invoice_status=	$("#InvoiceStatus").val();
		var invoice_date =	$("#InvoiceDate").val();

		// calculate today's date'
		var full_date = new Date()
		//console.log(fullDate);

		 
		//convert month to 2 digits
		var twoDigitMonth = ((full_date.getMonth().length+1) === 1)? (full_date.getMonth()+1) : '0' + (full_date.getMonth()+1);
		 
		var current_date = full_date.getDate() + " " + twoDigitMonth + " " + full_date.getFullYear();
		//console.log(currentDate);

	
		//Datepicker
		$('.datepicker').datepicker({
			format: "dd M yyyy",
    		autoclose: true			
		});

		//Tab navigation
		$('#myTabs a').click(function (e) {
		  e.preventDefault()
		  $(this).tab('show')
		})


		//hide notes submit buttons
		$('.Notes-Submit-Button').hide();
		
		//show notes submit buttons on text area change
		$('textarea').click(function() {
			$(this).siblings('p').children('.Notes-Submit-Button').show();
		});

		//confirm client billing guidelines submit
		$('#client-billing-guidelines-form').submit(function(e) {
	        var currentForm = this;
	        e.preventDefault();
	        
			var msg= "Update client billing guidelines?";	
				bootbox.confirm(msg, function(result) {
				    if (result) {
				        currentForm.submit();
				    } 
				}); //bootbox.confirm	        
		});	     

		// hide date field if cancelling invoice
		$("#InvoiceStatus").change(function() {
		  
		  if ($("#InvoiceStatus").val()=="CANCEL"){
		 	 $("#InvoiceDate").hide();	

		  // Show relevant date box otherwise
		  } else {
		 	 // Update date according to status = billeddate 
		 	 if ($("#InvoiceStatus").val()=="BILLED"){
		 	 	$("#InvoiceDate").val(invoice_date);	
		 	 
		 	 //or else current date
		 	 } else{
		 	 	//$("#InvoiceDate").val($.datepicker.formatDate('d M yyyy', new Date())) ;	
		 	 	$('.datepicker').datepicker('update', new Date());
		 	 } 
		 	 $("#InvoiceDate").show();
		  }
		});




		//confirm status change
		//http://stackoverflow.com/questions/11313586/confirm-form-submission-using-bootbox-confirm
		$('#invoice-status-form').submit(function(e) {
	        var currentForm = this;
	        e.preventDefault();
	        
	        //Status changed
	         if (invoice_status!=$("#InvoiceStatus").val()) {
	        
				var msg=  $("#InvoiceStatus").val()+" invoice?";	
				bootbox.confirm(msg, function(result) {
				    if (result) {
				        currentForm.submit();
				    } 
				}); //bootbox.confirm
			} //end if Status changed
			
			//Only date changed
			else if (invoice_date !=	$("#InvoiceDate").val()){

				var msg= "Change " + $("#InvoiceStatus").val()+" date?";	
				bootbox.confirm(msg, function(result) {
				    if (result) {
				        currentForm.submit();
				    } 
				}); //bootbox.confirm
				
			}
			
			//Nothing changed
			else if (invoice_date ==	$("#InvoiceDate").val() && invoice_status==$("#InvoiceStatus").val() )
			{
				
				bootbox.alert("Please change status and / or date first.");	
				
			}
		
		
		}); //$('#invoice-status-form').submit
			
		// payment js
		$("#InvoiceStatus").change(function() {
			var selectedOption = $(this).val();
			var paidAmountInput = $("#paidamount");
			
			if (selectedOption === "Partially Paid") {
			paidAmountInput.show();  // Show the input element
			} else {
			paidAmountInput.hide();  // Hide the input element
			}
		});
		
});   //$(function()     


