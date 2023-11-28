

$(function(){

	// ##################  On load  ################## 

	 maketablesortable();
	automateform();
	hidedetails ();


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
// ################## Make table sortable ##################
	function maketablesortable()
	{	
		//################## Activate table sorting via plugin
		jQuery.tablesorter.addParser({
		id: "commaDigit",
		is: function(s, table) {
			var c = table.config;
			return jQuery.tablesorter.isDigit(s.replace(/,/g, ""), c);
		},
		format: function(s) {
			return jQuery.tablesorter.formatFloat(s.replace(/,/g, ""));
		},
		type: "numeric"
	});
		
		
		$("#pageOutput table").tablesorter({ 
				// ################## Zebra widget adds proper shading to sorted table
				widgets: ['zebra'],
				// ################## Sort by date
			   headers:
			   {  
				1 : { sorter: "fancyNumber" },
				 4 : { sorter: "digit" },
				 5 : { sorter: "digit"  },
				 6 : { sorter: "digit"  },
				 7 : { sorter: "shortDate"},
				 8 : { sorter: false   }
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




// ################## automate form ##################

	 	function  automateform(){
			$("#clientcontrolsubmit").hide();
			$("#clientselector").change ( function () {
					$("#clientcontrol").submit();
				}
			);
		}


// ################## Hide monthtotals ##################
	function hidedetails ()
	{	
		//$("#pageOutput table, #pageOutput .invoices:eq(1) p").hide();
		$("#pageOutput .invoices:eq(0) h3").append("<a href='#' class='monthhide'> hide details</a>");
		$("#pageOutput .invoices:eq(1) p").hide();
		$("#pageOutput .invoices:eq(1) h5").append("<a href='#' class='monthmore'> show details</a>");
		showmonthprice ();
		hidemonthprice ();
	}

// ################## show month total ##################
	function showmonthprice ()
	{	
		$(".monthmore").click ( function () {
			$(this).parent().parent().children('table, p').show();
			$(this).parent().children('table, p').show();
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
			$(this).parent().parent().children('table,p').hide();
			$(this).after("<a href='#' class='monthmore'> show details</a>");
			$(this).remove();
			showmonthprice ();
			return false;
		  }
		);

	}


