$(function(){

			automateform();	
			doformatting();

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



// ################## Collapse year piles ##################
	function  doformatting()
	{	
		$('.yearpile li span').hide();
		$('.yearpile ul').css('height','150px').css('overflow-y','auto');
		$("<div><a href=\"#\" class=\"morebutton\">Show full details</a></div>").insertBefore('.yearpile:eq(0)');
		$("<div><a href=\"#\" class=\"lessbutton\">Show less details</a></div>").insertBefore('.yearpile:eq(0)');
		
		clickshowfield();
	}

// ################## activate client selector  ##################

	 	function  automateform(){
			$("#clientcontrolsubmit").hide();
			$("#clientselector").change ( function () {
					$("#clientcontrol").submit();
				}
			);
		}
		
// ################## show/hide details ##################

	function  clickshowfield()
	{	
		$(".lessbutton").hide();
		$('.morebutton').click(function() {
			$(".lessbutton").show();
			$(".morebutton").hide();
			
			$('.yearpile li span').show();
			$('.yearpile ul').css('height','auto')
		});		
		$('.lessbutton').click(function() {
			$(".lessbutton").hide();
			$(".morebutton").show();	
			$('.yearpile li span').hide();
			$('.yearpile ul').css('height','150px').css('overflow-y','auto');
		});	

	}