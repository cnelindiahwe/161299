$(function(){

			automateform();	

	// ##################  Entry form validation  ################## 
	$("#contactForm").validate({
			
		  highlight: function(element, errorClass) {
			$(element).css({ border: '1px solid #ff0'});
		  },
		  unhighlight: function(element, errorClass) {
			$(element).css({ border: '1px solid #fff'});
		  },
		  
		   rules: {
			CompanyName: "required",
			FirstName: "required",
			LastName: "required",
			Email1: {
			   required: true,
			   email: true
			 },


		   },			 
			errorPlacement: function(error, element) {
				offset = element.offset();
				error.insertBefore(element);
				error.addClass('message');  // add a class to the wrapper
				error.css('position', 'absolute');
				error.css('left', offset.left-13);
				error.css('top', offset.top+13);
			}


	});
	$.validator.messages.required = "Required";
	$.validator.messages.digits = "Digits only";
	$.validator.messages.number = "Number only";
	$.validator.messages.date = "Valid date required";


// ##################  End $(function(){  ################## 
									  
});

// ################## activate client selector  ##################

	 	function  automateform(){
			$("#clientsubmit").hide();
			$("#clientselector").change ( function () {
					$("#clientcontrol").submit();
				}
			);
		}


