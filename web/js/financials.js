$(function(){

	// ##################  On load  ################## 

	 maketablesortable();
	 hidedetails ();
	 automateform();


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
		$("#pageOutput table").tablesorter({ 
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
				sortList: [[2,0],[1,1]]

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
			$("#financialsmonthsubmit").hide();
			$("#financialsmonthpicker").change ( function () {
					$("#financialsmonthform").submit();
				}
			);
		}








