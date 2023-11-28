$(function(){

	// ##################  On load  ################## 


			//Make tables sortable
			maketablesortable();
			//Activate active contacts
		 	listcontactentries();
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

// ################## List past jobs by contact ##################
	function listcontactentries()
	{	
		$('.originatorview').click(function () {
			$('#activeContacts li').removeClass('highlight');
			$(this).parents('li').addClass('highlight');

			var trimtext =$(this).attr('href');
			trimtext =trimtext.replace('listpastjobs/','');

			$.ajax({
				type:"POST",
				url: BaseUrl+'trackingnew/ajax_listpastjobs',
				data: "originator="+trimtext,
				beforeSend:  function(data) {
					$('#pastJobs table').remove();
				},
				success: function(data) {
					$('#pastJobs').append(data);
					resetpage(trimtext);
				},
				error: function(data) {
					$('#pastJobs').append(data);
					}
			});	

			return false;

			
		});
		
	}
	
	function resetpage(Originator)
	{	
			maketablesortable();
			ajaxjoblinks();

	}
	
	