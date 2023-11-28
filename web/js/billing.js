$(function(){

	// ##################  On load  ################## 

	 hidemonthtotals();
	 maketablesortable();
	 

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


// ################## Make Honorees table sortable ##################
	function maketablesortable()
	{	
		//################## Activate table sorting via plugin
		$("table").tablesorter({ 
				// ################## Zebra widget adds proper shading to sorted table
				widgets: ['zebra'],
				// ################## Sort by date
			   headers:
			   {  
				 2 : { sorter: "digit"   },
				 3: { sorter: "digit"   },
				 4 : { sorter: "digit" },
				 5 : { sorter: "digit"  }
			   },
				//sortList: [[1,1]]

		}); 
		
		
		// ################## Pad left table headers to make space for sorting arrows
		$("th.header").css("padding-left","2em");
		
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
	function hidemonthtotals ()
	{	
		alert ('foo');
		//$(".billing #pageOutput div.monthtotal:first-child").siblings().children('table, p').hide();
		$("#pageOutput div.monthtotal:first-child").siblings().children('p:not(:nth-child(2n)),table').hide();
		$("#pageOutput div.monthtotal:first-child").siblings().children('p::nth-child(2n)').append("<a href='#' class='monthmore'>more</a>");
		showsinglemonthtotal ()
	}

// ################## show month total ##################
	function showsinglemonthtotal ()
	{	
		$(".monthmore").click ( function () {
			$(this).parent().siblings().show();
			$(this).parent('p').append("<a href='#' class='monthhide'>hide</a>");
			$(this).remove();
			hidesinglemonthtotal ();
			return false;
			}
			
		);
	}

// ################## hide month total ##################
	function hidesinglemonthtotal ()
	{	
			$(".monthhide").click ( function () {
				$(this).parent('p').siblings('table, p:not(:nth-child(2n))').hide();
				$(this).parent('p').append("<a href='#' class='monthmore'>more</a>");
				$(this).remove();
				showsinglemonthtotal ();
			    return false;
			  }
			);

	}
