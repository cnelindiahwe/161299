$(function(){

	// ##################  On load  ################## 

	$("tr.currrent").child("td").css("background-color","red");


	// ##################  Entry form validation  ################## 
	$("#editentry").validate({
							
		  highlight: function(element, errorClass) {
			$(element).css({ border: '1px solid red'});
		  },
		  unhighlight: function(element, errorClass) {
			$(element).css({ border: '1px solid white'});
		  },
		  
		   rules: {
			 Client: {
			   require_from_group: [1,"#Client"]
			 },
			 EntryDate: {
			   require_from_group: [1,"#EntryDate"],
			   date: true
			 },
			 Originator: {
			   require_from_group: [1,"#Originator"]
			 },
			 NewSlides: {
			   require_from_group: [1,".workdone"],
			   digits: true
			 	},
			 EditedSlides: {
			   require_from_group: [1,".workdone"],
			   digits: true
			 	},
			 Hours: {
			   require_from_group: [1,".workdone"],
			   digits: true
			 	}

		   },			 
			errorPlacement: function(error, element) {
				offset = element.offset();
				error.insertBefore(element);
				error.addClass('message');  // add a class to the wrapper
				error.css('position', 'absolute');
				error.css('left', offset.left);
				error.css('top', offset.top+20);
			}


	});
	$.validator.messages.required = "Required";
	$.validator.messages.digits = "Digits only";
	$.validator.messages.date = "Valid date required";




	// ##################  Delete button behavior  ################## 
	$('.button a.delete').click ( function () {
		$tempcol=$(this).parent('td').css("background-color");
		$(this).parent('td').siblings().css("background-color","red");
		$(this).parent('td').css("background-color","red");
		var answer = confirm("Delete entry?")
		if (!(answer)){
			$(this).parent('td').siblings().css("background-color",$tempcol);
			$(this).parent('td').css("background-color",$tempcol);
			return false;				   
		}

	});

	// ##################  Delete button behavior  ################## 
	/*$('.button a.edit').click ( function () {
		$tempcol=$(this).parent('td').css("background-color");
		$(this).parent('td').siblings().css("background-color","#66CC00");
		$(this).parent('td').css("background-color","#66CC00");
		
		return false;				   
	});
	*/
// ##################  End $(function(){  ################## 
});





// ################## ensure that the user fills in at least one of a group of fields ##################


jQuery.validator.addMethod("require_from_group", function(value, element, options) {
    numberRequired = options[0];
    selector = options[1];
    //Look for our selector within the parent form
    var validOrNot = $(selector, element.form).filter(function() {
         // Each field is kept if it has a value
         return $(this).val();
         // Set to true if there are enough, else to false
      }).length >= numberRequired;

    //The elegent part - this element needs to check the others that match the
    //selector, but we don't want to set off a feedback loop where all the
    //elements check all the others which check all the others which
    //check all the others...
    //So instead we
    //  1) Flag all matching elements as 'currently being validated'
    //  using jQuery's .data()
    //  2) Re-run validation on each of them. Since the others are now
    //     flagged as being in the process, they will skip this section,
    //     and therefore won't turn around and validate everything else
    //  3) Once that's done, we remove the 'currently being validated' flag
    //     from all the elements
    if(!$(element).data('being_validated')) {
    var fields = $(selector, element.form);
    //.valid() means "validate using all applicable rules" (which 
    //includes this one)
    fields.data('being_validated', true).valid();
    fields.data('being_validated', false);
    }
    return validOrNot;
     }, jQuery.format("Required"));


