$(function(){

	// ##################  On load  ################## 


			//Make tables sortable
			maketablesortable();
			//Past jobs control
		 	 pastjobscontrol();
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
	
		//Change view type
		$('#viewtype').change(function () {
				$('#pastJobs table').remove();
			 if ($('#viewtype').val()=="month") {
					$('#numberjobs, label[for="numberjobs"]').hide();
					
			 }else
			 {
					$('#numberjobs, label[for="numberjobs"]').show();
					if ($('#pastjobsclientContacts').length == 0) {
							$('#pastjobsclient').change();
					}	else {
						$('#pastjobsclientContacts').change();
					}
			 }
			});	
	
	
	
		//Change number of jobs shown
		$('#numberjobs').change(function () {
			 var dataforlist= "numjobs="+$('#numberjobs').val()+"&client='"+$('#pastjobsclient').val()+"'";
				$.ajax({
				type:"POST",
				url: BaseUrl+'trackingnew/ajax_listpastjobs',
				data:  dataforlist,
				beforeSend:  function(data) {
					$('#pastJobs table').remove();
				},
				success: function(data) {
					$('#pastJobs').append(data);
					resetpage();
				},
				error: function(data) {
					$('#pastJobs').append(data);
					}
			});	

			return false;
		});

		//Change client
		$('#pastjobsclient').change(function () {
			var pjclientame=$('#pastjobsclient').val().replace("&", "~");
			if (pjclientame!="" && pjclientame!="all") {
				pastjobscontactlist(pjclientame);
			}
			else {
				$('#pastjobsclientContacts').remove();	
			}
																		
			 var dataforlist= "numjobs="+$('#numberjobs').val()+"&client='"+pjclientame;
				$.ajax({
				type:"POST",
				url: BaseUrl+'trackingnew/ajax_listpastjobs',
				data:  dataforlist,
				beforeSend:  function(data) {
					$('#pastJobs table').remove();
				},
				success: function(data) {
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

// ##################  Add contact list filter in past jobs control ##################
	function pastjobscontactlist(pjclientame)
	{	

			var data='client='+pjclientame;
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
						$('#pastJobs .zowtrakui-topbar form').append(newdata);
						pastjobsclientContactsactivate(pjclientame);
					}
				})

	}

// ##################  Activate contact list filter in past jobs control ##################
	function 	pastjobsclientContactsactivate(pjclientame)
	{	

		//Change number of jobs shown
		$('#pastjobsclientContacts').change(function () {
			 var dataforlist= "numjobs="+$('#numberjobs').val()+"&client='"+pjclientame+"&Originator='"+$('#pastjobsclientContacts').val()+"'";
				$.ajax({
				type:"POST",
				url: BaseUrl+'trackingnew/ajax_listpastjobs',
				data:  dataforlist,
				beforeSend:  function(data) {
					$('#pastJobs table').remove();
				},
				success: function(data) {
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

// ##################  Reset page on past jobs change ##################
	function resetpage()
	{	
			maketablesortable();
			ajaxjoblinks();
	}
	
	