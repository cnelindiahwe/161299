$(function(){

	// ##################  On load  ################## 

	if ($('#DateIn').val()=="") {
		insertDate ();
	}
	$("#DateIn, #DateOut" ).datepicker({ 
				defaultDate: +7,
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'd-M-yy',
	});
	

	getClientContacts();
	viewTravelleroption ();
	
	$("#pageInput").resizable({ handles: 's,e', minHeight: 120});
	$("#pageOutput").resizable({ handles: 's,e', minHeight: 120});
	
	
	if ($("#pageOutput").height()>350){
		$("#pageOutput").height(350);
	}

	 if ($("#completedntries tbody").height()>150){
		 
		 $("#completedntries tbody").height(150);
	 }
	$("tr.currrent").css("background-color","red");


	// ##################  Entry form validation  ################## 
	$.validator.addMethod('isDate', function(value, element)
        {
            var isDate = false;
            try
            {
                $.datepicker.parseDate('d-M-yy', value);
                isDate = true;
            }
            catch (e)
            {

            }
            return isDate;
        });



$("#entryForm").validate({
							
		  
		   rules: {
			 Client: {
			   require_from_group: [1,".EntryClient",'fieldset']
			 	},
			 DateIn: {
			   require_from_group: [1,".EntryDate",'fieldset'],
			 	},
			 DateOut: {
			   require_from_group: [1,".Deadline",'fieldset'],
			  	},
			 Originator: {
			   require_from_group: [1,".Origin",'fieldset'],
			 	},
			 NewSlides: {
			   require_from_group: [1,".workdone",'fieldset'],
			   number: true
			 	},
			 EditedSlides: {
			   require_from_group: [1,".workdone",'fieldset'],
			   number: true
			 	},
			 Hours: {
			   require_from_group: [1,".workdone",'fieldset'],
			   number: true
			 	}

		   },
			 
			errorPlacement: function(error, element) {
				offset = element.offset();
				error.insertBefore(element);
				error.addClass('message');  // add a class to the wrapper
				error.css('position', 'absolute');
				error.css('float', 'right');
				//error.css('left', offset.left-13);
				error.css('margin-top', '1.5em');
			},

		  highlight: function(element, errorClass) {
			$(element).css({  backgroundColor: '#ff0'});
		  },
		  unhighlight: function(element, errorClass) {
			$(element).css({  backgroundColor: '#fff'});
		  }

	});
	$.validator.messages.required = "Required";
	$.validator.messages.digits = "Digits only";
   $.validator.messages.digits = "Digits only";
	$.validator.messages.date = "Valid date required";
 $("#DateIn").rules("add", { isDate: true, messages: {isDate: "Date to Validate is invalid."} });
 $("#DateOut").rules("add", { isDate: true, messages: {isDate: "Date to Validate is invalid."} });


	// ##################  Delete form value while loading new page ################## 
	/*$('.edit').click ( function () {
		 
			$('#newentry').children().children('input').val('');
			$('#newentry').children().children().children('input').val('');		
			$('#newentry #Status').selectOptions('');
	});//
*/


	// ##################  Load originator list on client change ################## 
	$('#Client').change( 
		function () {
			getClientContacts ();
			
		}
	);



	
// ##################  End $(function(){  ################## 
});

// ##################  Insert client timezone into form  ################## 
	function getClientTimeZone ($client)
	
	{
			 $.ajax({
			  type:"POST",
			  url: BaseUrl+'clients/ajaxclienttimezone',
			  data:$data,
			  success: function(data) {
				$("#TimeZoneIn").val(data);
				$("#TimeZoneOut").val(data);
			  }
			})
	}

// ##################  Insert client contacts dropdown into form  ################## 
	function getClientContacts ()
	
	{
			$client=$('#Client').val().replace("&", "~");
			$data='client='+$client;
			$current=($('#Originator').val());
			$.ajax({
				  type:"POST",
				  url: BaseUrl+'contacts/ajaxviewclientcontacts',
				  data:$data,
				  beforeSend:function() {
					  $('#clientContacts').remove();
				  },
				  success: function(data) {
						$('#Originator').remove();
						$('#originfield').append(data);
						$('#Originator').css("width","11.5em");
						$("#Originator").val($current);
						$('#Originator').change( function () {
							goAddContact ();
						})
				  }
  			})

		}

// ##################  Go to add contact if selected on dropdown ################## 
	function goAddContact ()
	
	{
		if ($('#Originator').val()=="newcontact"){
			var answer = confirm("Create New Contact?");
			if (answer){
				$.ajax({
					  type:"POST",
					  url: BaseUrl+'contacts/ajaxaddcontact',
					  data:$data,
					  success: function(data) {
						window.location = data
					  }
				});
			}
		}
		else if ($('#Originator').val()!=""){
			getContactTimeZone ($('#Originator').val())
		}

	}

// ##################  Insert contact timezone into form  ################## 
	function getContactTimeZone ($client)
	
	{
			 $data='Originator='+$('#Originator').val()+'&CompanyName='+$('#Client').val();
			 $.ajax({
			  type:"POST",
			  url: BaseUrl+'contacts/ajaxcontacttimezone',
			  data:$data,
			  success: function(data) {
				$("#TimeZoneIn").val(data);
				$("#TimeZoneOut").val(data);
			  }
			})
	}



// ##################  Generate view traveler option  ################## 
	function viewTravelleroption ()
	
	{
	$('#pageSidebar a').hover( 
		function () {
			$(this).append($("<span> View</span>"));
			viewTraveller ();
		}, 
		  function () {
			$(this).find("span:last").remove();
		}
	);

	}

	// ##################   view traveler click  ################## 
		function viewTraveller ()
		{
			$('#pageSidebar a span').click( 
				function () {
					openTraveller ($(this).parent("a").attr("href"));
					return false;
				}
			);
	
		}

	// ##################   open traveler  ################## 
		function openTraveller (url)
		{
				
				$data='entry='+url;
				
				$.ajax({
				  type:"POST",
				  url: BaseUrl+'tracking/ajaxviewtraveller',
				  data:$data,
				  success: function(data) {
					$('body .traveller').remove(); 
					$('body').append(data);
				  }
				});	
		}

// ##################  Insert client timezone into form  ################## 
	function getClientTimeZone ($client)
	
	{
			 $.ajax({
			  type:"POST",
			  url: BaseUrl+'clients/ajaxclienttimezone',
			  data:$data,
			  success: function(data) {
				$("#TimeZoneIn").val(data);
				$("#TimeZoneOut").val(data);
			  }
			})
	}
// ##################  Insert current date into form  ################## 
	function insertDate ()
	
	{
		//Insert current date into form   
		var months=new Array(13);
		months[1]="January";
		months[2]="February";
		months[3]="March";
		months[4]="April";
		months[5]="May";
		months[6]="June";
		months[7]="July";
		months[8]="August";
		months[9]="September";
		months[10]="October";
		months[11]="November";
		months[12]="December";
		var time=new Date();
		var lmonth=months[time.getMonth() + 1];
		var date=time.getDate();
		var year=time.getYear();
		if (year < 2000)
		year = year + 1900;   
		$('#DateIn').val(date+"/"+lmonth+"/"+year);
	
	}




// ################## ensure that the user fills in at least one of a group of fields ##################


jQuery.validator.addMethod("require_from_group", function(value, element, options) {
		numberRequired = options[0];
		selector = options[1];
		var commonParent = $(element).parents(options[2]);
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
		/*
		if(!$(element).data('being_validated')) {
			var fields = $(selector, element.form);
			//.valid() means "validate using all applicable rules" (which 
			//includes this one)
			fields.data('being_validated', true).valid();
			fields.data('being_validated', false);
			}
		*/
		return validOrNot;
     }, jQuery.format("Required"));


