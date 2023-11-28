$(function(){

	// ##################  On load  ################## 


	 maketablesortable();
	 

	// ##################  Delete button behavior  ################## 
	$('.button a.delete').click ( function () {
		$(this).parent('td').siblings().removeClass("rowhighlight");
		$(this).parent('td').removeClass("rowhighlight");
		

		$(this).parent('td').siblings().addClass("rowdelete");
		$(this).parent('td').addClass("rowdelete");
		var answer = confirm("Delete entry Permanently?")
		if (!(answer)){
			$(this).parent('td').siblings().removeClass("rowdelete");
			$(this).parent('td').removeClass("rowdelete");
			return false;				   
		}
	});//


	// ##################  Restore button behavior  ################## 
	$('.button a.restore').click ( function () {
		$(this).parent('td').siblings().removeClass("rowhighlight");
		$(this).parent('td').removeClass("rowhighlight");
		

		$(this).parent('td').siblings().addClass("rowdelete");
		$(this).parent('td').addClass("rowdelete");
		var answer = confirm("Restore entry?")
		if (!(answer)){
			$(this).parent('td').siblings().removeClass("rowdelete");
			$(this).parent('td').removeClass("rowdelete");
			return false;				   
		}
	});//



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


// ################## Make table sortable ##################
	function maketablesortable()
	{	
		//################## Activate table sorting via plugin
		$("#entriesdump").tablesorter({ 
				// ################## Zebra widget adds proper shading to sorted table
				widgets: ['zebra'],
				// ################## Sort by date
			   headers:
			   {  
				 3 : { sorter: "usLongDate"  },
				 5 : { sorter: "digit"  },
				 6 : { sorter: "digit"  },
				 5 : { sorter: "digit"   },
				 10 : { sorter: "digit"  }
			   },
				//sortList: [[1,1]],
				//debug: true 

		}); 
		
		$("#clientsdump").tablesorter({ 
				// ################## Zebra widget adds proper shading to sorted table
				widgets: ['zebra'],
				// ################## Sort by date
			   headers:
			   {  
				 2 : { sorter: "digit"  },
				 3 : { sorter: "digit"  },
				 4 : { sorter: "digit"  },
				 5 : { sorter: "digit"   },
				 6 : { sorter: "digit"  },
				 7 : { sorter: "digit"  },
				 8 : { sorter: "digit"   },
			   },
				//sortList: [[1,1]],
				//debug: true 

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



