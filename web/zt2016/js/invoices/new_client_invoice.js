$(function(){
	
 		//client dropdown
 		$('#client_dropdown_selector_submit').hide();
		$("#client_dropdown_selector").change ( function () {
				$("#client_dropdown_form").submit();
			}
		); 	

		//date pickers
		$('.datepicker').datepicker({
			format: "dd M yyyy",
    		autoclose: true			
		});


		//date pickers
		var entriestavble = $('#new-invoice-entries').dataTable( {
	        "sDom": "t",
	        "order":          [[ 1, "asc" ]],
	        //"scrollY":        "20em",
	        "scrollCollapse": true,
	        "paging":         false,
	        "info":           true,
	        "processing":     true,
	        "orderClasses":   false,
			"retrieve": true,
			"paging": false,
			columnDefs: 	  [ { targets: 'no-sort', orderable: false }],
			"bAutoWidth": false,
		}); //$('#new-invoice-entries').dataTable
	

 		//datesform submit on datepickers change
 		$('#Dates-Refresh-Submit').hide();
		$(".datepicker").change ( function () {
				$("#invoice-dates-form").submit();
			}
		); 	

		//confirm detail changes
		//http://stackoverflow.com/questions/11313586/confirm-form-submission-using-bootbox-confirm
		$('#invoice-dates-form').submit(function(e) {
	        var currentForm = this;
	        e.preventDefault();

			var msg=  "Update invoice details?";	
			bootbox.confirm(msg, function(result) {
			    if (result) {
			        currentForm.submit();
			    } 
			}); //bootbox.confirm
		}); //$('#invoice-status-form').submit


		//"Check all" buttom
		$('#exclude-header').prepend("<input type='checkbox' id='exclude-all-selector'>  ");
		$("#exclude-all-selector").click ( function (e) {
				$(".exclude-checkbox").prop('checked', this.checked);
			}); 		

		$(".exclude-checkbox").click ( function () {
				$("#exclude-all-selector").prop('checked', false);
			}
		); 	
		
		//Ensure that not all checkboxes are checked before submitting
		//http://stackoverflow.com/questions/5541387/check-if-all-checkboxes-are-selected
		$('#excludeform').submit(function(e) {
	        var currentForm = this;
	        e.preventDefault();

		    if ($('.exclude-checkbox:checked').length == $('.exclude-checkbox').length) {
				var msg=  "Please include at least one entry in the invoice.";	
				bootbox.alert(msg) 
		    } else {
			        currentForm.submit();
			}
		}); //$('#invoice-status-form').submit




});   //$(function()     


