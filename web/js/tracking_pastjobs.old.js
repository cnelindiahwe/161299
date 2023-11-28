$(function(){

	// ##################  On load  ################## 


			//Make tables sortable
			maketablesortable();
			//Past jobs control
		 	pastjobscontrol();
			
			//$('#pastjobsclient').change();
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

// ################## Past job control ##################
	function pastjobscontrol()
	{	
	
		//########################Change view type
		$('#viewtype').change(function () {
			$('#pastJobs table').remove();
			$('#pastJobs .zowtrakui-topbar form #CalendarMonth, #pastJobs zowtrakui-topbar form #CalendarYear').remove();
			if ($('#viewtype').val()=="month") {
				$('#numberjobs, label[for="numberjobs"]').hide();
			 }
			 else
			 {
					$('#numberjobs, label[for="numberjobs"]').show();
			 }
			 refreshpastjobs();
			});	
	
		//###########################Change number of jobs shown
		$('#numberjobs').change(function () {
			 refreshpastjobs();
		});

		//###############################Change client
		$('#pastjobsclient').change(function () {
																					
			//client contact list management																		
			var pjclientame=$('#pastjobsclient').val().replace("&", "~");
			if (pjclientame!="" && pjclientame!="all") {
				pastjobscontactlist(pjclientame);
			}
			else {
				$('.pastjobsclientContacts').remove();	
			}
			
			if ($('#viewtype').val()=="list") {
			 	var dataforlist= "numjobs="+$('#numberjobs').val()+"&client='"+pjclientame;
			 	var urlforlist= BaseUrl+'tracking/ajax_listpastjobs';
			}
			
			else if ($('#viewtype').val()=="month") {
			 	var dataforlist= "client='"+pjclientame;
				if ($('#CalendarMonth').length != 0 && $('#CalendarYear').length !=0) {
					var dataforlist= dataforlist+"&CalendarMonth="+$('#CalendarMonth').val()+"&CalendarYear="+$('#CalendarYear').val();
				}
			 	var urlforlist= BaseUrl+'tracking/ajax_pjmonthview';
			}
				$.ajax({
				type:"POST",
				url: urlforlist,
				data:  dataforlist,
				beforeSend:  function(data) {
					$('#pastJobs table').remove();
					$('#pastJobs').append("<img src='"+BaseUrl+"web/img/anim/loading_large.gif' class='loadingimg'>");
				},
				success: function(data) {
					$('.loadingimg').remove();
					$('#pastJobs').append(data);
					resetpage();
				},
				error: function(data) {
					$('#pastJobs').append(data);
					}
			});
			return false;
		});
		
	}
	
// ##################  Refresh screen after control change 
	function refreshpastjobs()
	{	
			if ($('#pastjobsclientContacts').length == 0) {
					$('#pastjobsclient').change();
			}	else {
				$('#pastjobsclientContacts').change();
			}

	}

// ##################  Add contact list filter in past jobs control 
	function pastjobscontactlist(pjclientame)
	{	

			var data='client='+pjclientame+'& inactive=yes';
			$.ajax({
					type:"POST",
					url: BaseUrl+'contacts/ajax_viewclientcontacts',
					data:data,
					beforeSend:function() {
						$('.pastjobsclientContacts').remove();
					},
					success: function(data) {
						var newdata = data.replace(/Originator/gi, "pastjobsclientContacts");					
						var newdata = newdata.replace("Origin ", "");		
						var newdata = newdata.replace("<option></option>", "<option value=\"\">All</option>");	
						
						$('#pastJobs .zowtrakui-topbar form').append("<label for=\"pastjobsclientContacts\" class=\"pastjobsclientContacts\">Originator:</label>");
						$('#pastJobs .zowtrakui-topbar form').append(newdata);
						pastjobsclientContactsactivate(pjclientame);
						resetpage();
					}
				});

	}

// ##################  Activate contact list filter in past jobs control 
	function 	pastjobsclientContactsactivate(pjclientame)
	{	

		// ################## Change contact
		$('#pastjobsclientContacts').change(function () {

			if ($('#viewtype').val()=="list") {
			 	var dataforlist= "numjobs="+$('#numberjobs').val()+"&client='"+pjclientame+"&Originator='"+$('#pastjobsclientContacts').val()+"'";
			 	var urlforlist= BaseUrl+'tracking/ajax_listpastjobs';
			}
			
			else if ($('#viewtype').val()=="month") {
			 	var dataforlist= "client='"+pjclientame+"&Originator='"+$('#pastjobsclientContacts').val()+"'";
				if ($('#CalendarMonth').length != 0 && $('#CalendarYear').length !=0) {
				dataforlist= dataforlist+"&CalendarMonth="+$('#CalendarMonth').val()+"&CalendarYear="+$('#CalendarYear').val();
				}
			 	var urlforlist= BaseUrl+'tracking/ajax_pjmonthview';
			}
				$.ajax({
				type:"POST",
				url: urlforlist,
				data:  dataforlist,
				beforeSend:  function(data) {
					$('#pastJobs table').remove();
				},
				success: function(data) {
					//$('#pastJobs').append(dataforlist);
					$('#pastJobs').append(data);
					monthviewnavcontrol();
					resetpage();
				},
				error: function(data) {
					$('#pastJobs').append(data);
					}
			});	

			return false;
		});

	}

// ##################  Reset page on past jobs change 
	function resetpage()
	{	
		monthviewnavcontrol();
		maketablesortable();
		ajaxjoblinks();
		// Hide tentatives from ongoing list 
		hidetentatives();
	}
	

// ##################  Add contact list filter in past jobs control ##################
	function monthviewnavcontrol()
	{	
			
			$('.monthviewnav').click ( function () {
					var pjclientame=$('#pastjobsclient').val().replace("&", "~");
					var dataforlist= "client='"+pjclientame+"'";
					if ($('#pastjobsclientContacts').length != 0) {
						dataforlist=dataforlist+"&Originator='"+$('#pastjobsclientContacts').val()+"'";
					}
					calendardata=$(this).attr("href").split('/');
					calendarmonth=calendardata[calendardata.length-1];
					calendaryear=calendardata[calendardata.length-2];
					dataforlist=dataforlist+"&CalendarMonth="+calendarmonth+"&CalendarYear="+calendaryear;
					$.ajax({
						type:"POST",
							url: BaseUrl+'tracking/ajax_pjmonthview',
							data:  dataforlist,
							beforeSend:  function(data) {
								$('#pastJobs table').remove();
							},
							success: function(data) {
								$('#CalendarMonth, #CalendarYear,.loading').remove();
								$('#pastJobs .zowtrakui-topbar form').append('<input type="hidden" name="CalendarMonth" id="CalendarMonth" value="'+calendarmonth+'">');
								$('#pastJobs .zowtrakui-topbar form').append('<input type="hidden" name="CalendarYear" id="CalendarYear" value="'+calendaryear+'">');
								$('#pastJobs').append(data);
								resetpage();
							},
							error: function(data) {
								$('#pastJobs').append(data);
								}
					});
					return false;
				});

	}	