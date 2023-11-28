$(function(){

	// ##################  On load  ################## 

	 maketablesortable();
	 hidedetails ();
	 automateform();
	 graphtables ();


	// ##################  Row highlight on hover ################## 
	$('tr').hover( 
		  function () {
			$(this).children('td').siblings().addClass("rowhighlight");
		  },
		  function () {
			$(this).children('td').siblings().removeClass("rowhighlight");
		  }
	);
	

// ##################  End $(function(){  ################## 
});

	// ##################  Add special format parser for totals ################## 
	$.tablesorter.addParser({
        id: "fancyNumber",
        is: function(s) {
            return /^[0-9]?[0-9,\.]*$/.test(s);
        },
        format: function(s) {
            return $.tablesorter.formatFloat(s.replace(/,/g, ''));
        },
        type: "numeric"
    });
	
// ################## Make Honorees table sortable ##################
	function maketablesortable()
	{	
		//################## Activate table sorting via plugin
		$("#pageOutput table.originatorsdata").tablesorter({ 
				// ################## Zebra widget adds proper shading to sorted table
				widgets: ['zebra'],
				// ################## Sort by date
			   headers:
			   {  
				 3 : { sorter: "fancyNumber"   },
				 /*2 : { sorter: "digit"   },
				 3: { sorter: "digit"   },
				 4 : { sorter: "digit" },
				 5 : { sorter: "digit"  },
				 6 : { sorter: "digit"  },
				 7 : { sorter: "shortDate"},
				 8 : { sorter: false   }*/
			   },
				sortList: [[4,0],[2,1]]

		}); 
		
		$("#pageOutput table.clientsdata").tablesorter({ 
				// ################## Zebra widget adds proper shading to sorted table
				widgets: ['zebra'],
				// ################## Sort by date
			   headers:
			   {  
				 1 : { sorter: "fancyNumber"   },
				 /*2 : { sorter: "digit"   },
				 3: { sorter: "digit"   },
				 4 : { sorter: "digit" },
				 5 : { sorter: "digit"  },
				 6 : { sorter: "digit"  },
				 7 : { sorter: "shortDate"},
				 8 : { sorter: false   }*/
			   },
				sortList: [[3,0],[1,1]]

		}); 
		
		
		// ################## Pad left table headers to make space for sorting arrows
		$("#pageOutput table thead tr th.header").css("padding-left","2em");
		
		$('th.header').hover(
  			function () {
				$(this).addClass("tableheaderhighlight");
			},
 			function () {
				$(this).removeClass("tableheaderhighlight");
			}
		); 
	}

// ################## Hide monthtotals ##################
	function hidedetails ()
	{	
		//$(".billing #pageOutput div.monthtotal:first-child").siblings().children('table, p').hide();
		$("#pageOutput table").hide();
		$("#pageOutput div").children('.title').append("<a href='#' class='monthmore'> more</a>");
		showmonthprice ();
	}

// ################## show month total ##################
	function showmonthprice ()
	{	
		$(".monthmore").click ( function () {
			//$(this).remove();
			$(this).parent().parent().children('table').show();
			$(this).parent().parent().children('.title').append("<a href='#' class='monthhide'> hide</a>");
			$(this).remove();

			hidemonthprice ();
			return false;
			}
			
		);
	}

// ################## hide month total ##################
	function hidemonthprice ()
	{	
		$(".monthhide").click ( function () {
			$(this).parent().parent().children('table').hide();
			$(this).parent().parent().children('.title').append("<a href='#' class='monthmore'> more</a>");
			$(this).remove();
			showmonthprice ();
			return false;
		  }
		);

	}


// ################## automate form ##################

	 	function  automateform(){
			$("#timeframesubmit").hide();
			$("#Timeframe").change ( function () {
					$("#timeframecontrol").submit();
				}
			);
		}


// ################## Hide monthtotals ##################
	function hidedetails ()
	{	
		$("#pageOutput table").hide();
		$("#pageOutput .breakdown h3").append("<a href='#' class='monthmore'> show details</a>");
		showmonthprice ();
	}

// ################## show month total ##################
	function showmonthprice ()
	{	
		$(".monthmore").click ( function () {
			$(this).parent().parent().children('.originatorsdata,.clientsdata').show();
			$(this).after("<a href='#' class='monthhide'> hide details</a>");
			$(this).remove();
			hidemonthprice ();
			return false;
			}
			
		);
	}

// ################## hide month total ##################
	function hidemonthprice ()
	{	
		$(".monthhide").click ( function () {
			$(this).parent().parent().children('.originatorsdata,.clientsdata').hide();
			$(this).after("<a href='#' class='monthmore'> show details</a>");
			$(this).remove();
			showmonthprice ();
			return false;
		  }
		);

	}

// ################## create graph tables ##################
	function graphtables ()
	{	
		//clients
		$('.breakdown:eq(1)').css('min-height','320px');
		var newtable=$('table.clientsdata').clone();
		newtable.find("tbody tr").each(function(){
			$(this).find("td:eq(1),td:eq(2),td:eq(3),td:eq(4)").remove();
			$str=$(this).find("td:eq(0)").html();
			$str2=$str.replace(/,/g, '');
			$(this).find("td:eq(0)").html($str2);
		});
		newtable.find("thead tr").each(function(){
			$(this).find("th:eq(2),th:eq(3),th:eq(4),th:eq(5)").remove();
		});
		newtable.attr("class","graphtable");
		$(".breakdown:eq(1) h3").after(newtable);
		
		
		//Originators
		$('.breakdown:eq(0)').css('min-height','320px');
		var newtable=$('table.originatorsdata').clone();
		newtable.find("tbody tr").each(function(){
			$(this).find("td:eq(0),td:eq(2),td:eq(3),td:eq(4),td:eq(5)").remove();
			$str=$(this).find("td:eq(0)").html();
			$str2=$str.replace(/,/g, '');
			$(this).find("td:eq(0)").html($str2);
		});
		newtable.find("thead tr").each(function(){
			$(this).find("th:eq(1),th:eq(3),th:eq(4),th:eq(5),th:eq(6)").remove();
		});

		newtable.attr("class","graphtable");
		$(".breakdown:eq(0) h3").after(newtable);
		
		//show graphs
		$('.graphtable').visualize({
			type:'pie',
			height:'350',
			width:'350'					 
		});		


	}
			
