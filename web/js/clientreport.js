$(function(){

	// ##################  On load  ################## 


	maketablesortable();
	automateform();
	showgraph();
	
// ##################  End $(function(){  ################## 
});


// ################## Make Honorees table sortable ##################
	function maketablesortable()
	{	
		//################## Activate table sorting via plugin
		$("#clientreportdata").tablesorter({ 
				// ################## Zebra widget adds proper shading to sorted table
				widgets: ['zebra'],
				// ################## Sort by date
			   headers:
			   {  
				 1 : { sorter: "usLongDate"  },
				 7 : { sorter: false   },
				 8 : { sorter: false   }
			   }

		}); 
		
		
		// ################## Pad left table headers to make space for sorting arrows
		$("#clientreportdata thead tr th.header").css("padding-left","2em");
		
		$('#clientreportdata th.header').hover(
  			function () {
				$(this).addClass("tableheaderhighlight");
			},
 			function () {
				$(this).removeClass("tableheaderhighlight");
			}
		); 
	}


// ################## activate client selector  ##################

	 	function  automateform(){
			$("#reportclientsubmit").hide();
			$("#reportsclient").change ( function () {
				$("#clientcontrol").submit();																 
				}
			);

		}
		
		
// ################## graph ##################

	 	function  showgraph(){
		
			var newtable=$('table').clone();
			newtable.attr("id", 'clonetable');
			
			
			

			//$("#clonetable tbody").append($("#clonetable tbody tr").get().reverse());
			newtable.find("tbody tr").each(function(){
				$(this).find("td:eq(2),td:eq(3),td:eq(4)").remove();
				$(this).find("td:eq(2),td:eq(3),td:eq(4)").remove();
			});
			newtable.find("thead tr").each(function(){
				$(this).find("th:eq(2),th:eq(3),th:eq(4),th:eq(5)").remove();
			});
			newtable.find("thead tr").each(function(){
				$(this).find("th:eq(0)").html('Hours');
				$(this).find("th:eq(1)").html('Jobs');
			});

			$('#clientreportdata').before(newtable);
			
			var rows = newtable.find('tbody > tr').get();
			newtable.find('tbody tr').remove();
			//rows.reverse();																				
			newtable.append(rows);
			$("#clonetable").hide();
			
			
				$('#clonetable').visualize({
					type:'line',
					height:'300',
					width:'800',
					parseDirection: 'y'
				});
			
		}
		
		
		jQuery.fn.reverse = [].reverse;