$(function(){

	// ##################  On load  ################## 


			//Make tables sortable
			maketablesortable();	 
			
			// Activate ajax clicks on job links 
			ajaxjoblinks();
			
		//load traveler for new jobs
		$(".newjob").click( 
				function () {
					openTravellerNew ();
					return false;
				}
			);
		
		//$("#pastJobs" ).css("border","3px solid #aaa").css("width","auto").draggable().resizable();

// ##################  End $(function(){  On load  ################## 
});


// ################## Make past jobs table sortable ##################
	function maketablesortable()
	{	
		// Activate table sorting via plugin
		$("#pastJobs table").tablesorter({ 
				// Zebra widget adds proper shading to sorted table
				widgets: ['zebra'],
				//  Sort by date
			  
				headers:
			   {  
				 5 : { sorter: "shortDate"},
				 7 : { sorter: false   }
			   },
				 /**/
				sortList: [[5,6]]

		}); 

			// ################## Pad left table headers to make space for sorting arrows
			$("#pastJobs table thead tr th.header").css("padding-left","2em");
			
			$('#pastJobs th.header').hover(
					function () {
					$(this).addClass("tableheaderhighlight");
				},
				function () {
					$(this).removeClass("tableheaderhighlight");
				}
			);

	}

// ################## Travellers ##################


// ################## Activate ajax clicks on job links ##################
	function ajaxjoblinks()
	{	
		// load traveler for existing jobs
		$('.projectlink').unbind('click');
		$(".projectlink").click( 
				function () {
					$(this).addClass('activejob'); 
					$(this).parents('td').addClass('activejob'); 
					$(this).parents('td').siblings().addClass('activejob'); 
					openTraveller ($(this).attr("href"));
					return false;
				}
			);
		

	}

		//################## load traveler for new jobs
		function openTravellerNew ()
		{
					$.ajax({
						type:"POST",
						url: BaseUrl+'trackingnew/ajaxaddtravellernew',
						success: function(data) {
							createTraveller (data, 'New');
						},
						error: function(data) {
							//$('body .traveller').remove(); 
							alert (data);
							}
					});	

					return false;
			}


		//################## load traveler for existing jobs
		function openTraveller (url)
		{
				formdata='entry='+url;
				$.ajax({
				  type:"POST",
				  url: BaseUrl+'trackingnew/ajaxviewtravellernew',
				  data:formdata,
				  success: function(data) {
						createTraveller (data,'Existing');
				  },
					error: function(data) {
						//$('body .traveller').remove(); 
						alert (data);
					  }
				});	
		}
		
	// ##################   fill traveler  ################## 
		function createTraveller (data,type)
		{
						$('body').append(data);
						$("body .traveller" ).draggable();
						travellerclose();
						travellersubmit($("body .traveller:last form"),type);
						trashentry();
						changeclient ('null');

						$("body .traveller:last form .DateIn, body .traveller:last form .DateOut" ).datepicker({ 
									defaultDate: +7,
									showOtherMonths: true,
									selectOtherMonths: true,
									dateFormat: 'd-M-yy',
						});	
						
						if (type=='Existing'){
							duplicateentry($('body .traveller:last form'));
							getClientContacts ($('body .traveller:last form'));
							//$('body .traveller:last form #Client').change();
						}
						else if (type=='New'){
							insertDate ($('body .traveller:last form'));
							insertTime ($('body .traveller:last form'));
							hidetimein ($('body .traveller:last form'));
						}
		}
	// ################## submit traveller button ##################
		function travellersubmit(traveller,type)
		{	
				if (type=='New') {
					posturl= BaseUrl+'trackingnew/ajaxaddentrynew';	
				}
				else {
					posturl= BaseUrl+'trackingnew/ajaxupdateentrynew';
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
									//alert($(this).name);
									//alert (data);
									submitcleanup();
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
	function submitcleanup()
	{	
		
		// refresh completed jobs
		$.ajax({
						type:"POST",
						url: BaseUrl+'trackingnew/ajaxrefreshongoing ',
						async: false,
						success: function(data) {
							$("#pastJobs table").remove();
							$("#pastJobs").append(data);
							maketablesortable();							 
						},
						error: function(data) {
							$("#pastJobs table").remove();
							$("#pastJobs").append(data);
							}
					});
		// refresh pending jobs
		$.ajax({ 
						type:"POST",
						url: BaseUrl+'trackingnew/ajaxrefreshpending ',
						async: false,
						success: function(data) {
							$("#ongoing a.projectlink").remove();
							$("#ongoing").append(data);
							ajaxjoblinks();
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
							url: BaseUrl+'trackingnew/ajaxtrashentrynew ',
							data:formdata,
							success: function(data) {
								submitcleanup();
								ajaxjoblinks();
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
						traveller.find('.DateIn').val('');
						traveller.find('.TimeIn').val('');
						traveller.find('.DateOut').val('');
						traveller.find('.DateIn').val('');
						insertDate (traveller);
						insertTime(traveller);
						if (formvalidation (traveller)) {
			
								formdata=traveller.serialize();
									$.ajax({
										type:"POST",
										url: BaseUrl+'trackingnew/ajaxaddentrynew',
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

// ################## close traveller button ##################
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
				
				
					// ##################  Load originator list on client change ################## 
				function changeclient ()
				
				{			
					  $(".EntryClient").each(function() {
									var theValue = $(this).val();
									$(this).data("oldvalue", theValue);
						});

					
					$('.EntryClient').change( 
							function () {
								var previousValue = $(this).data("oldvalue");
								var currentOrig=$(this).parents('form').find('.EntryClient').val();
								//if (previousValue!=currentOrig) {
									client=$(this).val().replace("&", "~");
									data='client='+client;
									getClientTimeZone (data,'both',$(this).parents('form'));
									getClientContacts ($(this).parents('form'));
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
							alert="current";
							$.ajax({
									type:"POST",
									url: BaseUrl+'contacts/ajaxviewclientcontacts',
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
							url: BaseUrl+'clients/ajaxclienttimezone',
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
							url: BaseUrl+'clients/ajaxclienttime',
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
								url: BaseUrl+'clients/ajaxclientdate',
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
							traveller.find('.DateOut').parent('fieldset').before('<fieldset><a href="#" class="viewdatein">Show Date/Time In</a></fieldset>');
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