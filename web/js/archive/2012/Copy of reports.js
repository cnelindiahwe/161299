$(function(){

	// ##################  On load  ################## 

	
	 hidemonthtotals ();
	 maketablesortable();
	 automateform();
	 
	$('.monthtotal:eq(0) table')
	//	.visualize({ type: 'pie',parseDirection:'y'});
		.visualize({type:'pie',height:'250'});
		//.appendTo('.monthtotal:eq(0)');
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
		//$("#pageOutput div.monthtotal:nth-child(2)").siblings().children('p:not(:nth-child(2n)),table').hide();
		//$("#pageOutput div.monthtotal:nth-child(2)").siblings().children('p::nth-child(2n)').append("<a href='#' class='monthmore'>more</a>");
		showsinglemonthtotal();
	}

// ################## show month total ##################
	function showsinglemonthtotal ()
	{	
		$(".monthmore").click ( function () {
			$(this).siblings().show();
				$(this).siblings('table').visualize({type:'pie',height:'250'})
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
				$(this).siblings('table').hide();
				$(this).siblings('.visualize').remove();
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
			$("#reportclientsubmit").hide();
			$("#reportsclient").change ( function () {
					var currentval=$("#reportsclient").val();
					if (currentval==''){
						posturl= BaseUrl+'reports';
					} else {
						posturl= BaseUrl+'reports/clientreport';					
					}
					$.ajax({
							type:"POST",
							url: posturl,
							data:'reportsclient='+currentval,
							beforeSend:function() {

							},
							success: function(data) {
								$('.monthtotal').remove();
								$('#pageOutput').append(data);
								 hidemonthtotals ();
								 maketablesortable();
							}
						})
				}
			);
		}