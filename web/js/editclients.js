$(function(){

	// ##################  On load  ################## 

	 doinserts();
	 automateform();	 
	 hidefields();	 


	// ##################  Entry form validation  ################## 
	$("#newclient").validate({
							
		  highlight: function(element, errorClass) {
			$(element).css({ border: '1px solid #ff0'});
		  },
		  unhighlight: function(element, errorClass) {
			$(element).css({ border: '1px solid #fff'});
		  },
		  
		   rules: {
			CompanyName: "required",
			ZOWContact: "required",
			Currency: "required",
			 BasePrice: {
			   required: true,
			   number: true
			 },
			 VolDiscount1Trigger: {
			  number: true
			 },
			 VolDiscount2Trigger: {
			   number: true
			 },
			 VolDiscount3Trigger: {
			   number: true
			 },
			 VolDiscount4Trigger: {
			   number: true
			 },
			 VolDiscount1Price: {
			  number: true
			 },
			 VolDiscount2Price: {
			   number: true
			 },
			 VolDiscount3Price: {
			   number: true
			 },
			 VolDiscount4Price: {
			   number: true
			 },
			 PriceEdits: {
			   number: true
			 },
			 Retainer: {
			   number: true
			 },
			 OfficeVersion: {
			   number: true
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

	
	// ##################  trash button behavior  ################## 
	$('a.trashclient').click ( function () {
		var answer = confirm("Trash client?")
		if (!(answer)){
			return false;				   
		}
	});//

});// ##################  end onload  ################## 





// ################## Insert defaults into form ##################
	function  doinserts()
	{	
		if ($('#PriceEdits').val()=="") {
			$('#PriceEdits').val(1);
		}
		if ($('#OfficeVersion').val()=="") {
			$('#OfficeVersion').val(2007);
		}
	}

// ################## automate client selector form ##################

	 	function  automateform(){
			$("#clientcontrolsubmit").hide();
			$("#clientselector").change ( function () {
					$("#clientcontrol").submit();
				}
			);
		}
	
	// ################## Insert "more / less" ##################
	/*function  hidefields()
	{	
		$('h4').each(function(index) { 
			$(this).append(' <a href="#"	class="'+$(this).attr('class')+'">Less</a>');
		});
	
		
		$('fieldset.pricinginfo, fieldset.formbuttons').show();
		clickshowfield();
	}
	// ################## Insert defaults into form ##################
	function  clickshowfield()
	{	
		$('h4 a').click(function() {
				var elementClassName = $(this).attr('class');
				console.log (elementClassName);
				if ($(this).text()=="More") { $(this).text('Less') } else $(this).text('More')
				$('fieldset.'+ elementClassName +',div.'+ elementClassName +', p.'+ elementClassName).toggle();
		});	
		$('a.contactlist').trigger('click'); 
	}
*/