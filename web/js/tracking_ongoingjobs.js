$(function(){

	// ##################  On load  ################## 

			
			// Activate ajax clicks on job links 
			ajaxjoblinks();
			// Show sort filter
			sortfilter();

		// Hide tentatives from ongoing list 
		hidetentatives();
			
			


// ##################  End $(function(){  On load  ################## 
});



// ################## Activate ajax clicks on job links ##################
	function ajaxjoblinks()
	{	
		// load traveler for existing jobs
		$('.projectlink').unbind('click');
		$(".projectlink").click( 
				function () {
					if ($(this).parents('#monthview').length) {
					
					} else {
						$('.activejob').removeClass('activejob'); 
						$(this).addClass('activejob'); 
						$(this).parents('td').addClass('activejob'); 
						$(this).parents('td').siblings().addClass('activejob');
					}
					//alert ($(this).parents('div').attr("id"));
					//openTraveller ($(this).attr("href"),$(this).parents('div').attr("id"));
					openTraveller ($(this).attr("href"),$(this).parents('div').attr("id"));
					return false;
				}
			);

	}

// ################## hide tentatives from ongoing list and add button ##################
	function hidetentatives()
	{	
		$("#ongoing a.showtentative, #ongoing a.hidetentative").remove();
		var ntentative = $("#ongoing a.tentative").length;
		if (ntentative >0) {
			$("#ongoing a.tentative").hide();
			$("#ongoing").append("<a href='#' class='showtentative'>Show "+ ntentative+" tentative job(s)</a>");
			$("#ongoing").append("<a href='#' class='hidetentative'>Hide tentatives</a>");
			$("#ongoing .hidetentative").hide();			
			$(".showtentative").click(
				function () {
					$("#ongoing .hidetentative, #ongoing a.tentative").show();
					$("#ongoing .showtentative").hide();
				}
			);
			$(".hidetentative").click(
				function () {
					$("#ongoing .hidetentative, #ongoing a.tentative").hide();
					$("#ongoing .showtentative").show();
				}
			);
		}
	}

// ################## add sort filter ##################
	function sortfilter(filtersort)
	{	
		var nEntries = $("#ongoing a").length;
		if (nEntries>1) {
			filtersort = typeof filtersort != 'undefined' ? filtersort : "phase";	
			//Add filter
			$("#sortfilterform").remove();
			var sortfilter = "<form id='sortfilterform'><label for 'ongoingfilter'>Sort by:</label><select id='ongoingfilter'>";
			
			sortfilter = sortfilter+"<option value='phase'>Phase</option>";
			sortfilter = sortfilter+"<option value='deadline' ";
			if (filtersort!="phase") {sortfilter = sortfilter+"selected='selected'";}
			sortfilter = sortfilter+">Deadline</option>";
			sortfilter = sortfilter+"</select></form>";
			$("#newjobbuttons").append(sortfilter);
			//Activate filter
			$("#ongoingfilter").change(
				function () {
					showsorted($("#ongoingfilter").val());
				}	
			);
		}
	}
// ################## hide tentatives from ongoing list and add button ##################
	function showsorted(filtersort)
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
					$("#pageOutput").empty();
					$("#pageOutput").append(data);
					// refresh completed jobs /reset page
					maketablesortable();
					ajaxjoblinks();
					managerbutton();
					sortfilter(filtersort);
					hidetentatives();
					newJobButton ();
					resetPageswitcher();
				},
				error: function(data) {
					$("#ongoing a.projectlink").remove();
					$("#ongoing").append(data);
					}
			});

	}