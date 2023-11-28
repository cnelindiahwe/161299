$(function(){

	// ##################  On load  ################## 

	
	 hidemonthtotals ();
	 maketablesortable();
	 automateform();
	 addcurrentgraph();
		sixmonthgraph();
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


// ################## Make month table sortable ##################
	function maketablesortable()
	{	
		//################## Activate table sorting via plugin
		$("table").tablesorter({ 
				// ################## Zebra widget adds proper shading to sorted table
				widgets: ['zebra'],
				// ################## Sort by date
			   headers:
			   {  
				 1 : { sorter: "digit"   },
				 2 : { sorter: "digit"   },
				 3: { sorter: "digit"   },
				 4 : { sorter: "digit" },
				 5 : { sorter: "digit"  }
			   },
				sortList: [[1,1]]

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
		$("#pageOutput div.monthtotal table").hide();
		$("#pageOutput div.monthtotal:eq(0) table").show();
		$("#pageOutput div.monthtotal table").before("<a href='#' class='monthmore'>more</a>");
		$("#pageOutput div.monthtotal:eq(0) .monthmore").remove();
		showsinglemonthtotal();
	}

// ################## show month total ##################
	function showsinglemonthtotal ()
	{	
		$(".monthmore").click ( function () {
			$(this).parents('.monthtotal').css('min-height','320px');
			$(this).siblings().show();
			$(this).siblings('table').trigger("update"); 
			$(this).siblings('table').trigger("sorton",[2,2]); 
			//create graph data table
			var newtable=$(this).siblings('table').clone();
			newtable.find("tbody tr").each(function(){
				$(this).find("td:eq(1),td:eq(2),td:eq(3)").remove();
			});
			newtable.find("thead tr").each(function(){
				$(this).find("th:eq(2),th:eq(3),th:eq(4)").remove();
			});
			newtable.attr("class","graphtable");
			$(this).parents(".monthtotal").append(newtable);
			$(this).siblings(".graphtable").hide();
			
			
				$(this).siblings('.graphtable').visualize({
					type:'pie',
					height:'250',
					width:'250'					 
				});
			$(this).after("<a href='#' class='monthhide'>hide</a>");
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
				$(this).parents('.monthtotal').css('min-height','3em');
				$(this).siblings('table').hide();
				$(this).siblings('.visualize, .graphtable').remove();
				$(this).parent('p').siblings('table, p:not(:nth-child(2))').hide();
				$(this).after("<a href='#' class='monthmore'>more</a>");
				$(this).remove();
				showsinglemonthtotal ();
			    return false;
			  }
			);

	}


		
// ################## activate client selector  ##################

	 	function  automateform(){
			$("#worktypesubmit,#reportclientsubmit").hide();
			$("#Current_Client").change ( function () {
				$("#clientcontrol").submit();																 
				}
			);
			$("#WorkType").change ( function () {
				$("#worktypecontrol").submit();																 
				}
			);
		}
// ################## add current month graph  ##################

	 	function  addcurrentgraph(){
			if ($('.monthtotal:eq(0) table').length > 0) { // check that table exists
				$('.monthtotal:eq(0)').css('min-height','320px');
				var newtable=$('.monthtotal:eq(0) table').clone();
				newtable.find("tbody tr").each(function(){
					$(this).find("td:eq(1),td:eq(2),td:eq(3)").remove();
				});
				newtable.find("thead tr").each(function(){
					$(this).find("th:eq(2),th:eq(3),th:eq(4)").remove();
				});
				newtable.find("thead tr").each(function(){
					$(this).find("th:eq(2),th:eq(3),th:eq(4)").remove();
				});
				newtable.attr("class","graphtable");
				$(".monthtotal:eq(0)").append(newtable);
				$(".monthtotal:eq(0) .graphtable").hide();
				
				
					$('.monthtotal:eq(0) .graphtable').visualize({
						type:'pie',
						height:'250',
						width:'250'					 
					});
			}
		}	
		
// ################## create 6 month graph  ##################

	 /*	function  sixmonthgraph(){
			
			var newtable='<div id="sixmonthdata"><table id="sixmonthtable"><thead><tr><th>Month</th><th>Total</th></tr></thead><tbody>';
			$($("#pageOutput h3").get().reverse()).each(function(){
				 tablevalues=($(this).html()).split(" - ");
				 tabletotals=(tablevalues[1]).split(" ");
				 newtable= newtable+'<tr><th scope="row">'+tablevalues[0]+'</th><td>'+tabletotals[0]+'</td></tr>';
			});
			newtable=newtable+'</tbody></table></div>';
			$('#pageOutput div.monthtotal:eq(0)').before(newtable);
				var cssObj = {
				'margin-left' : '1em',
			}
			$('#sixmonthdata').css(cssObj).prepend("<h3>Totals last 6 months</h3><a href='#' class='sixmonthshow'>Show</a>");
			$('#sixmonthtable').hide();
			sixmonthgraphshow();
			sixmonthgraphhide();
		}				
	// ################## show 6 month graph  ##################

	 	function  sixmonthgraphshow(){
			$('.sixmonthshow').click(function(){
				$(this).before("<a href='#' class='sixmonthhide'>Hide</a>");
				sixmonthgraphhide();
				$(this).remove();
				var cssObj = {
				'height' : '220px',
				'padding-bottom':'1em',
			}
			$('#sixmonthdata').css(cssObj);
				$('#sixmonthtable').visualize({
					//type:'line',
					height:'150',
					width:'800',
					parseDirection: 'y',
					appendKey: false,
					appendTitle: false,
					barMargin: 35
				});
			});
		}	
		
	// ################## hide  6 month graph  ##################

	 	function  sixmonthgraphhide(){
			$('.sixmonthhide').click(function(){
				$('#sixmonthdata .visualize').remove();
				$(this).before("<a href='#' class='sixmonthshow'>Show</a>");
				sixmonthgraphshow();
				$(this).remove();
				$('#sixmonthdata').css('height','3em');
			});
		}	*/