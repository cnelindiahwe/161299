

$(function(){

	// ##################  On load  ################## 

	maketablesortable();
	automateform();
	hidenewinvoicedetails ();
	manageoriginators();

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
				 0 : { sorter: false },
				 1 : { sorter: false },
				 2 : { sorter: false  },
				 3 : {sorter: "shortDate" },
				 4 : { sorter: "shortDate"}
			   }

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
	function hidenewinvoicedetails ()
	{	
		$("#pageOutput table#currententries").hide();
		$("#pageOutput table#currententries").before("<a href='#' class='detailsmore'> show details</a>");
		showentries ();
	}

// ################## show month total ##################
	function showentries ()
	{	
		$(".detailsmore").click ( function () {
			$("#pageOutput table#currententries").show();
			$(this).after("<a href='#' class='detailshide'> hide details</a>");
			$(this).remove();
			hideentries ();
			return false;
			}
			
		);
	}

// ################## hide month total ##################
	function hideentries ()
	{	
		$(".detailshide").click ( function () {
			$("#pageOutput table#currententries").hide();
			$(this).after("<a href='#' class='detailsmore'> show details</a>");
			$(this).remove();
			showentries();
			return false;
		  }
		);

	}

// ################## hide originators column ##################
	function manageoriginators()
	{	
		$('#pastinvoices th:nth-child(2)').hide();
		$('#pastinvoices td:nth-child(2)').hide();

		$("#pastinvoices h3").after("<a href='#' class='originatorsmore'> Show originators</a>");
		showoriginators ();
	}

// ################## show month total ##################
	function showoriginators ()
	{	
		$(".originatorsmore").click ( function () {
			$('#pastinvoices th:nth-child(2)').show();
			$('#pastinvoices td:nth-child(2)').show();
			$(this).after("<a href='#' class='originatorsless'> Hide Originators</a>");
			$(this).remove();
			hideoriginators ();
			return false;
			}
			
		);
	}

// ################## hide month total ##################
	function hideoriginators ()
	{	
		$(".originatorsless").click ( function () {
			$('#pastinvoices th:nth-child(2)').hide();
			$('#pastinvoices td:nth-child(2)').hide();
			$(this).after("<a href='#' class='originatorsmore'> show details</a>");
			$(this).remove();
			showoriginators();
			return false;
		  }
		);

	}