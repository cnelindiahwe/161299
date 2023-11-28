$(function(){

	// ##################  On load  ################## 
	$("body").hide();	
	 insertDate ();
	 insertTime ();
	$("#DateIn, #DateOut" ).datepicker({ 
				defaultDate: +7,
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'd-M-yy',
	});
	//$("#pageInput").resizable({ handles: 's,e', minHeight: 120});
	//$("#pageOutput").resizable({ handles: 's,e', minHeight: 120});
	 
	//viewTravelleroption ();

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
		$.validator.messages.number = "Number only";
		$.validator.messages.date = "Valid date required";
	
		 $("#DateIn").rules("add", { isDate: true, messages: {isDate: "Date to Validate is invalid."} });
		 $("#DateOut").rules("add", { isDate: true, messages: {isDate: "Date to Validate is invalid."} });
 
	// ##################  Day highlight on hover (calendar) ################## 
		$('#calendar td').hover( 
			  function () {
				$(this).addClass("calendarhighlight");
			  },
			  function () {
				$(this).removeClass("calendarhighlight");
			  }
		);


	// ##################  remove calendar highlight on click ################## 
		$('#calendar td a').click( 
			
			  function () {
				  $(this).parent('div').parent('td').removeClass("calendarhighlight");
			  }
		);


	// ##################  Load originator list on client change ################## 
			$('#Client').change( 
				function () {
					client=$('#Client').val().replace("&", "~");
					data='client='+client;
					getClientTimeZone (data,'both');
					getClientContacts (data);
					
				}
			);


	// ##################  Change time when changing timezonein ################## 
		$('#TimeZoneIn').change( 
			function () {
					getClientDateTime ('in')
			}
		);

	// ##################  Change time when changing timezoneout ################## 
		$('#TimeZoneOut').change( 
			function () {
					getClientDateTime ('out')
			}
		);

	$("body").show();
// ##################  End $(function(){  ################## 
});



// ##################  Generate view traveler option  ################## 
	/*function viewTravelleroption ()
	
	{
	$('#ongoing a').hover( 
		function () {
			$(this).prepend($("<span> View</span>"));
			viewTraveller ();
		}, 
		  function () {
			$(this).find("span:last").remove();
		}
	);

	}*/

	// ##################   view traveler click  ################## 
	/*	function viewTraveller ()
		{
			$('#ongoing a span').click( 
				function () {
					openTraveller ($(this).parent("a").attr("href"));
					return false;
				}
			);
	
		}
*/
	// ##################   open traveler  ################## 
	/*	function openTraveller (url)
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
*/
// ##################  Insert client timezone into form  ################## 
	function getClientTimeZone (data,InOut)
	
	{
			 $.ajax({
			  type:"POST",
			  url: BaseUrl+'clients/ajaxclienttimezone',
			  data:data,
			  success: function(data) {
				  if (InOut=="both" || InOut=="in") {
					$("#TimeZoneIn").val(data);
				  }
				 if (InOut=="both" || InOut=="out") {
					$("#TimeZoneOut").val(data);
				  }
				getClientDateTime(InOut);
			  }
			})
	}

// ##################  Insert client date and time into form  ################## 
	function getClientDateTime (InOut)
	
	{
		 
			  if (InOut=="both" || InOut=="in") {
				tz="TimeZoneIn="+$("#TimeZoneIn").val();
			  }
			  else if ( InOut=="out") {
				tz="TimeZoneIn="+$("#TimeZoneOut").val();
			  }
	 
			 $.ajax({
			  type:"POST",
			  url: BaseUrl+'clients/ajaxclienttime',
			  data: tz,
			  success: function(data) {
				  if (InOut=="both" || InOut=="in") {
					$("#TimeIn").val(data);
				  }
				  if (InOut=="both" || InOut=="out") {
					$("#TimeOut").val(data);
				  }
				$.ajax({
				  type:"POST",
				  url: BaseUrl+'clients/ajaxclientdate',
				  data: tz,
				  success: function(data) {
				  if (InOut=="both" || InOut=="in") {
					$("#DateIn").val(data);
				  }
				   if (InOut=="both" || InOut=="out") {
					$("#DateOut").val(data);
				  }
				 }
				})
			  }
			})
			 
	}



// ##################  Insert client contacts dropdown into form  ################## 
	function getClientContacts (client)
	
	{
			$.ajax({
				  type:"POST",
				  url: BaseUrl+'contacts/ajaxviewclientcontacts',
				  data:data,
				  beforeSend:function() {
					  $('#clientContacts').remove();
				  },
				  success: function(data) {
						$('#Originator').remove();
						$('#originfield').append(data);
						$('#Originator').css("width","11.5em");
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
				getClientDateTime ("both");
			  }
			})
	}


// ##################  Insert current date into form  ################## 
	function insertDate ()
	
	{
		//Insert current date into form   
		var months=new Array(13);
		months[1]="Jan";
		months[2]="Feb";
		months[3]="Mar";
		months[4]="Apr";
		months[5]="May";
		months[6]="Jun";
		months[7]="Jul";
		months[8]="Aug";
		months[9]="Sep";
		months[10]="Oct";
		months[11]="Nov";
		months[12]="Dec";
		var time=new Date();
		var lmonth=months[time.getMonth() + 1];
		var date=time.getDate();
		var year=time.getYear();
		if (year < 2000)
		year = year + 1900;  
		if ($('#DateIn').val()=="") {
			$('#DateIn').val(date+"-"+lmonth+"-"+year);
		}
		
		if ($('#DateOut').val()=="") {
			$('#DateOut').val(date+"-"+lmonth+"-"+year);
		}
	}


// ##################  Insert current time into form  ################## 
	function insertTime ()
	
	{

		var today=new Date();
		var h=today.getHours();
		var m=today.getMinutes();
		//var s=today.getSeconds();
		// add a zero in front of numbers<10
		m=checkTime(m);
		//s=checkTime(s);
		if ($('#TimeIn').val()=="") {
			$('#TimeIn').val(h+":"+m);
		}
		if ($('#TimeOut').val()=="") {
			$('#TimeOut').val(h+":"+m);
		}
		
		function checkTime(i)
		{
		if (i<10)
		  {
		  i="0" + i;
		  }
		return i;
		}
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


