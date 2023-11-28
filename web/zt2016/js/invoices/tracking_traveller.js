$(function(){

	// ##################  On load  ################## 

			newJobButton ();
			//load traveler for new jobs
			

// ##################  End $(function(){  On load  ################## 
});

// ################## Activate new job button ##################

function newJobButton ()
		{
			$(".newjob").click( 
					function () {
						openTravellerNew ();
						return false;
					}
			);
		}

// ################## Travellers ##################



		//################## load traveler for new jobs
		function openTravellerNew ()
		{
					$('body .traveller').remove(); 
					$.ajax({
						type:"POST",
						url: BaseUrl+'tracking/ajax_addtravellernew',
						beforeSend:  function(data) {
							$('body').append('<div class="traveller"></div>');
						},
						success: function(data) {
							createTraveller (data, 'New');
						},
						error: function(data) {
							alert (data);
							}
					});	

					return false;
			}


		//################## load traveler for existing jobs
		function openTraveller (url,jobwindow)
		{
				
				$('body .traveller').remove(); 
				formdata='entry='+url;
				$.ajax({
				  type:"POST",
				  url: BaseUrl+'tracking/ajax_viewtravellernew',
				  data:formdata,
					beforeSend:  function(data) {
						$('body').append('<div class="traveller"></div>');
					},
				  success: function(data) {
						createTraveller (data,'Existing',jobwindow);
				  },
					error: function(data) {
						//$('body .traveller').remove(); 
						alert (data);
					  }
				});	
		}
		
	// ##################   fill traveler  ################## 
		function createTraveller (data,type,jobwindow)
		{
						
						$('body .traveller:last').append(data);
						$("body .traveller" ).draggable();
						travellerclose();
						travellersubmit($("body .traveller:last form"),type,jobwindow);
						trashentry();
						changeClientActivate();
 						addclientmaterials ($('body .traveller:last form'));
						$("body .traveller:last form .DateIn, body .traveller:last form .DateOut" ).datepicker({ 
									defaultDate: +7,
									showOtherMonths: true,
									selectOtherMonths: true,
									dateFormat: 'd-M-yy',
						});	
						$("body .traveller:last form .TimeOut,body .traveller:last form .TimeIn").timePicker({
							step: 15
						});
						
						if (type=='Existing'){
							duplicateentry($('body .traveller:last form'));
							getClientContacts ($('body .traveller:last form'));
							hideemptynotes($('body .traveller:last'));
							
						}
						else if (type=='New'){
							insertDate ($('body .traveller:last form'));
							insertTime ($('body .traveller:last form'));
							hidetimein ($('body .traveller:last form'));
						}
		}
	// ################## submit traveller button ##################
		function travellersubmit(traveller,type,jobwindow)
		{	

				if (type=='New') {
					posturl= BaseUrl+'tracking/ajax_addentrynew';	
				}
				else {
					posturl= BaseUrl+'tracking/ajax_updateentrynew';
					nOngoingItems = ($('#ongoing a').length);
				}
				traveller.submit(function() {

				if (formvalidation (traveller)) {
	
						formdata=traveller.serialize();
							$.ajax({
								type:"POST",
								url: posturl,
								data:formdata,
	
								beforeSend: function() {
									//traveller.parents(".traveller").empty();
									traveller.parents(".traveller").addClass('loading');
									},
	
								success: function(data) {
									//alert (data);
									submitcleanup(jobwindow);
									
								},
								error: function(data) {
									alert (data);
									}
							});
							traveller.parents(".traveller").remove();
						}
						return false;
				});
			}


// ################## submit traveller cleanup ##################
	function submitcleanup(jobwindow,filtersort)
	{	

		filtersort = typeof filtersort != 'undefined' ? filtersort : "phase";
		
		$.ajax({ 
				type:"POST",
				url: BaseUrl+'tracking/ajax_refreshongoing',
				data: {
					"filtersort":filtersort
				},
				async: false,

				success: function(data) {
					var nEntries= ($('#ongoing a').length);
					$("#pageOutput").empty();
					$("#pageOutput").append(data);
					if ($('#ongoing a').length!=nEntries) {
						// refresh completed jobs /reset page
						refreshpastjobs();
						
					}
					else if (jobwindow=="pastJobs") {
						refreshpastjobs();
					}
					maketablesortable();
					ajaxjoblinks();
					sortfilter(filtersort);
					managerbutton();
					newJobButton ();
					resetPageswitcher();
				},
				error: function(data) {
					$("#ongoing a.projectlink").remove();
					$("#ongoing").append(data);
					}
			});
		
		}

// ################## trashentry ##################
	function trashentry()
	{	
		
		$(".trashEntry").click( function () {
																			
		// Confirmation
			var answer = confirm("Trash entry?")
				if (answer){
					formdata= "id="+$("input#id").val();
					$.ajax({ 
							type:"POST",
							url: BaseUrl+'tracking/ajax_trashentrynew ',
							data:formdata,
							success: function(data) {
								
								submitcleanup();
							},
							error: function(data) {
								alert (data);
								}
						});		
				$(this).parents(".traveller").remove();
				}
				return false;
				}
			);
	
		}

// ################## duplicateentry ##################
	function duplicateentry(traveller)
	{	
	

				traveller.find(".formbuttons :nth-child(2)").after('<a href="#" class="duplicateentry">Duplicate Entry</a>');
				traveller.find(".duplicateentry").click( function () {
				var answer = confirm("Duplicate entry?")
					if (answer){
						traveller.find("#Status").val("SCHEDULED");
						traveller.find('.TimeOut').val('');
						traveller.find('.TimeIn').val('');
						traveller.find('.DateOut').val('');
						traveller.find('.DateIn').val('');
						insertDate (traveller);
						insertTime(traveller);
						if (formvalidation (traveller)) {
			
								formdata=traveller.serialize();
									$.ajax({
										type:"POST",
										url: BaseUrl+'tracking/ajax_addentrynew',
										data:formdata,
										beforeSend: function() {
											traveller.parents(".traveller").remove();	
										},
										success: function(data) {
											submitcleanup();
											itemtoedit='a[href$="'+BaseUrl+'editentry/'+data+'"]';
											$(itemtoedit).click();
										},
										error: function(data) {
											alert (data);
											}
									});
									
								}	
						}
			});
	
		}

// ################## cancel traveller button ##################
	function travellerclose()
	{	
		//################## close traveller
		$(".cancelEdit").click( 
				function () {
					//Deactive highlight
					actionurl =$(this).parents("form").attr( 'action' );
					var items = actionurl.split('/');
					itemtoedit='a[href$="'+BaseUrl+"editentry/"+items[items.length-1]+'"]';
					$(itemtoedit).removeClass('activejob');
					$(itemtoedit).parents('td').removeClass('activejob'); 
					$(itemtoedit).parents('td').siblings().removeClass('activejob'); 
					//Remove traveler
					$(this).parents(".traveller").remove();
					$(".time-picker").remove();
					return false;
				}
			);
	
		}
		
		
		// ##################  FORMS  ################## 		
		
					// ##################  Insert current date into form  ################## 
				function insertDate (traveller)
				
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
					if (traveller.find('.DateIn').val()=="") {
						traveller.find('.DateIn').val(date+"-"+lmonth+"-"+year);
					}
					
					if (traveller.find('.DateOut').val()=="") {
						traveller.find('.DateOut').val(date+"-"+lmonth+"-"+year);
					}
				}
			
			
			// ##################  Insert current time into form  ################## 
				function insertTime (traveller)
				
				{
			
					var today=new Date();
					var h=today.getHours();
					var m=today.getMinutes();
					//var s=today.getSeconds();
					// add a zero in front of numbers<10
					m=checkTime(m);
					//s=checkTime(s);
					if (traveller.find('.TimeIn').val()=="") {
						traveller.find('.TimeIn').val(h+":"+m);
					}
					if (traveller.find('.TimeOut').val()=="") {
						traveller.find('.TimeOut').val(h+":"+m);
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
				
				
					// ##################  Load originator list and reset timezone on client change ################## 
				function changeClientActivate ()
				
				{			
					 /* $(".EntryClient").each(function() {
									var theValue = $(this).val();
									$(this).data("oldvalue", theValue);
						});
*/

					
					$('.EntryClient').change( 
							function () {
								
								//var previousValue = $(this).data("oldvalue");
								//var currentOrig=$(this).parents('form').find('.EntryClient').val();
								//if (previousValue!=currentOrig) {
									client=$(this).val().replace("&", "~");
									data='client='+client;
									getClientTimeZone (data,'both',$(this).parents('form'));
									getClientContacts ($(this).parents('form'));
									alert ("Time reset due to client change");
									
								//}
							}
						);
					}


				// ##################  Insert client contacts dropdown into form  ################## 
					function getClientContacts (activeform)
					
					{
							var client=$(activeform).find('.EntryClient').val().replace("&", "~");
							var data='client='+client;
							var current=$(activeform).find('.Originator').val();
							$.ajax({
									type:"POST",
									url: BaseUrl+'contacts/ajax_viewclientcontacts',
									data:data,
									beforeSend:function() {
										$('#clientContacts').remove();
									},
									success: function(data) {
										$(activeform).find('.Originator').remove();
										$(activeform).find('.originfield').append(data);
										$(activeform).find('.Originator').val(current);
										$(activeform).find('.Originator').change( function () {
											goAddContact ();
										})
									}
								})
				
						}


			// ##################  Insert client timezone into form  ################## 
				function getClientTimeZone (data,InOut,traveler)
				
				{
						 $.ajax({
							type:"POST",
							url: BaseUrl+'clients/ajax_clienttimezone',
							context:traveler,
							data:data,
							success: function(data) {
								if (InOut=="both" || InOut=="in") {
								$(this).find(".TimeZoneIn").val(data);
								}
							 if (InOut=="both" || InOut=="out") {
								$(this).find(".TimeZoneOut").val(data);
								}
							getClientDateTime(InOut,traveler);
							}
						})
				}
			
			
			// ##################  Insert client date and time into form  ################## 
				function getClientDateTime (InOut,traveller)
				
				{
			
								if (InOut=="both" || InOut=="in") {
							tz="TimeZoneIn="+traveller.find(".TimeZoneIn").val();
							}
							else if ( InOut=="out") {
							tz="TimeZoneIn="+traveller.find(".TimeZoneOut").val();
							}
							
						 $.ajax({
							type:"POST",
							url: BaseUrl+'clients/ajax_clienttime',
							data: tz,
							context:traveller,
							success: function(data) {
								if (InOut=="both" || InOut=="in") {
								$(this).find(".TimeIn").val(data);
								}
								if (InOut=="both" || InOut=="out") {
								$(this).find(".TimeOut").val(data);
								}
							$.ajax({
								type:"POST",
								url: BaseUrl+'clients/ajax_clientdate',
								data: tz,
								success: function(data) {
								if (InOut=="both" || InOut=="in") {
								$(this).find(".DateIn").val(data);
								}
								 if (InOut=="both" || InOut=="out") {
								$(this).find(".DateOut").val(data);
								}
							 }
							})
							}
						})
						 
				}
				
	
				
					// ##################  Entry form validation  ################## 
				
				function formvalidation (traveller)
				
				{
		
				var validationhi = { backgroundColor : "#ff0"};
				var validationlo = { backgroundColor : "#fff"};
				
				if (traveller.find('.NewSlides').val()=="" && traveller.find('.EditedSlides').val()=="" && traveller.find('.Hours').val()=="")
				{
					traveller.find('.NewSlides').css(validationhi);
					traveller.find('.EditedSlides').css(validationhi);
					traveller.find('.Hours').css(validationhi);
				}
				else {
					traveller.find('.NewSlides').css(validationlo);
					traveller.find('.EditedSlides').css(validationlo);
					traveller.find('.Hours').css(validationlo);

					if (traveller.find('.EntryClient').val()=="")
					{
						traveller.find('.EntryClient').css(validationhi);
					}
					else {
						traveller.find('.EntryClient').css(validationlo);
			
	
						if (traveller.find('.Originator').val()=="")
						{
							traveller.find('.Originator').css(validationhi);
						}
						else {
							traveller.find('.Originator').css(validationlo);
							return true;
						}	
					}
				}
}
				


// ##################  Hide unused elements  ################## 

			// ##################  Hide date/time in in new jobs  ################## 
				function hidetimein (traveller)
				
				{
							traveller.find('.TimeIn').parent('fieldset').hide();
							traveller.find('.DateIn').parent('fieldset').hide();
							traveller.find('.TimeZoneIn').parent('fieldset').hide();
							traveller.find('.ProjectName').parent('fieldset').before('<fieldset><a href="#" class="viewdatein">Show Date/Time In</a></fieldset>');
							viewdatein(traveller);
							
			}
			
			// ##################  Show date/time in in new jobs  ################## 
				function viewdatein (traveller)
				
				{
				$('.viewdatein').click( 
						function () {	
							$(this).parents('.traveller').find('.DateIn').parents('fieldset').show();
							$(this).parents('.traveller').find('.TimeIn').parents('fieldset').show();
							$(this).parents('.traveller').find('.TimeZoneIn').parents('fieldset').show();
							$(this).remove();
							return false;
					}
				);
			}
			
			// ##################  Hide empty notes fields  ################## 
				function hideemptynotes (traveller)
				
				{
					if (traveller.find('.ContactNotes').val()=="") {
						traveller.find('.ContactNotes').hide();
					}
					if (traveller.find('.ClientNotes').val()=="") {
						traveller.find('.ClientNotes').hide();
					}
					if (traveller.find('.ZOWNotes').val()=="") {
						traveller.find('.ZOWNotes').hide();
					}
					showemptynotes (traveller);
				}
			// ##################  Show empty notes fields  ################## 
				function showemptynotes (traveller)
				
				{
					//add view buttons
					traveller.find('.ContactNotes,.ClientNotes,.ZOWNotes').
					parents('fieldset').
					children('label').append('<a href="#">View</a>');
					traveller.find('.ContactNotes,.ClientNotes,.ZOWNotes').
					parents('fieldset').
					children('label').css('display:block');
					//activate view buttons
					traveller.find('.notesfield').parents('fieldset').children('label').click( 
						function () {
							$(this).parents('fieldset').children('.notesfield').toggle();
							return false;
						}
					);
				}
				
	// ##################  Add client materials  ################## 
		function addclientmaterials (traveller)
		
		{
			traveller.append('<fieldset><label>Client Materials <a href="#">View</a></label><div class="clientmaterials"></div></fieldset>');
			traveller.find('.clientmaterials').parents('fieldset').children('label').click( 
				function () {
					listclientmaterials (traveller);
					return false;
				}
			);
		}
		
	// ##################  List client materials  ################## 
	function listclientmaterials (traveller)
	{

			var clientcode=traveller.find('#clientcode').val();
			$.ajax({
				type:"POST",
				url: BaseUrl+'clients/ajax_clientmaterialslist/'+clientcode+'/'+SuperUserFlag,
				beforeSend:  function(data) {
					traveller.find('.clientmaterials').append('<img src="'+BaseUrl+'web/img/anim/loading.gif">');
				},
				success: function(data) {
					traveller.find('.clientmaterials').empty().append(data);
					//traveller.find('.clientmaterials').parents('fieldset').children('label').children('a').remove();
					traveller.find('.clientmaterials').parents('fieldset').children('label').unbind( "click" );
					traveller.find('.clientmaterials').parents('fieldset').children('label').click( 
						function () {
							$(".clientmaterials").toggle();
							return false;
						}
					);					
				},
				error: function(data) {
					//$('body .traveller').remove(); 
					alert (data);
					}
			});	
		}