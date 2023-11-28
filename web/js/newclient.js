$(function(){

	// ##################  On load  ################## 

	 doinserts();
	 automateform();

	// ##################  Entry form validation  ################## 
	$("#clientform").validate({
							
		  highlight: function(element, errorClass) {
			$(element).css({ border: '1px solid #ff0'});
		  },
		  unhighlight: function(element, errorClass) {
			$(element).css({ border: '1px solid #fff'});
		  },
		  
		   rules: {
			CompanyName: "required",
			ZOWContact: "required",
			ClientCode:"required",
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




	/*$('#PricePer0Hours').change(function() {
										 alert ('fooo');
		var price=$('#PricePer0Hours').val();
		if ($('#PricePer10Hours').val()=="" || $('#PricePer10Hours').val()>price) {
			$('#PricePer10Hours').val(price);
		}
		if ($('#PricePer20Hours').val()=="" || $('#PricePer20Hours').val()>price) {
			$('#PricePer20Hours').val(price);
		}
		if ($('#PricePer30Hours').val()=="" || $('#PricePer30Hours').val()>price) {
			$('#PricePer30Hours').val(price);
		}
		if ($('#PricePer40Hours').val()=="" || $('#PricePer40Hours').val()>price) {
			$('#PricePer40Hours').val(price);
		}
	});

	$('#PricePer10Hours').change(function() {
		alert ('fooo');
		var price=$('#PricePer10Hours').val();
		if ($('#PricePer20Hours').val()>price) {
			$('#PricePer20Hours').val(price);
		}
		if ($('#PricePer30Hours').val()>price) {
			$('#PricePer30Hours').val(price);
		}
		if ($('#PricePer40Hours').val()>price) {
			$('#PricePer40Hours').val(price);
		}
	});
	
	// ##################  Delete button behavior  ################## 
	$('.button a.delete').click ( function () {
		$(this).parent('td').siblings().removeClass("rowhighlight");
		$(this).parent('td').removeClass("rowhighlight");
		

		$(this).parent('td').siblings().addClass("rowdelete");
		$(this).parent('td').addClass("rowdelete");
		var answer = confirm("Trash client?")
		if (!(answer)){
			$(this).parent('td').siblings().removeClass("rowdelete");
			$(this).parent('td').removeClass("rowdelete");
			return false;				   
		}
					   

	});//


	// ##################  Row highlight on hover ################## 
	$('tr').hover( 
		  function () {
			$(this).children('td').siblings().addClass("rowhighlight");
		  },
		  function () {
			$(this).children('td').siblings().removeClass("rowhighlight");
		  }
	);
	*/
// ##################  End $(function(){  ################## 
									  
});




// ################## Insert defaults into form ##################
	function  doinserts()
	{	
		if ($('#PriceEdits').val()=="") {
			$('#PriceEdits').val('0.5');
		}
		/*if ($('#OfficeVersion').val()=="") {
			$('#OfficeVersion').val(2007);
		}
		*/
	}

// ################## activate client selector  ##################

	 	function  automateform(){
			$("#clientcontrolsubmit").hide();
			$("#clientselector").change ( function () {
					$("#clientcontrol").submit();
				}
			);
		}