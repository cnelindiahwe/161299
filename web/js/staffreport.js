$(function(){

	// ##################  On load  ################## 

	 maketablesortable();
	 hidedetails ();
	 automateform();
	draw_treemap();

	// ##################  Row highlight on hover ################## 
	$('tr').hover( 
		  function () {
			$(this).children('td').siblings().addClass("rowhighlight");
		  },
		  function () {
			$(this).children('td').siblings().removeClass("rowhighlight");
		  }
	);
		$(window).resize(function() {
			$("#svgcontainer").remove();
		 	draw_treemap();
		});		

// ##################  End $(function(){  ################## 
});


	
// ################## Make Honorees table sortable ##################
	function maketablesortable()
	{	
		//################## Activate table sorting via plugin
		$("#pageOutput table").tablesorter({ 
				// ################## Zebra widget adds proper shading to sorted table
				widgets: ['zebra']
				// ################## Sort by date
			  /* headers:
			   {  
				 1 : { sorter: "digitr"   },
				 2 : { sorter: "digit"   },
				 3: { sorter: "digit"   },
				 4 : { sorter: "digit" },
				 5 : { sorter: "digit"  },
				 6 : { sorter: "digit"  },
				 7 : { sorter: "shortDate"},
				 8 : { sorter: false   }
			   },*/
				//sortList: [[2,0],[1,1]]

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
		$("#pageOutput div").has('table').children('table').hide();
			$("#pageOutput div").has('table').children('.title').append("<a href='#' class='monthmore'> more</a>");
				showmonthdetails  ();
	}

// ################## show month total ##################
	function showmonthdetails ()
	{	
		$(".monthmore").click ( function () {
			//$(this).remove();
			$(this).parent().parent().children('table').show();
			$(this).parent().parent().children('.title').append("<a href='#' class='monthhide'> hide</a>");
			$(this).remove();

			hidemonthdetails ();
			return false;
			}
			
		);
	}

// ################## hide month total ##################
	function hidemonthdetails  ()
	{	
		$(".monthhide").click ( function () {
			$(this).parent().parent().children('table').hide();
			$(this).parent().parent().children('.title').append("<a href='#' class='monthmore'> more</a>");
			$(this).remove();
			showmonthdetails  ();
			return false;
		  }
		);

	}


// ################## automate form ##################

	 	function  automateform(){
			$("#reportmonthsubmit").hide();
			$("#reportmonthpicker").change ( function () {
					$("#reportmonthform").submit();
				}
			);
			$("#staffswitcherselect").change ( function () {
					$("#reportmonthform").submit();
				}
			);
		}


// ################## d3 ##################

	function draw_treemap() {
		
		var svgwidth = $(window).width()-($(window).width()*.05),
	    svgheight = $(window).height()-($(window).height()*.35),
	    svgcolor = d3.scale.category10();
	    
	    var ratio= 1,
		 splittables=new Array("ScheduledBy","WorkedBy","ProofedBy"),
		 mytotal=0,
		 root=0,
		 node,
		 x = d3.scale.linear().range([0, svgwidth]),
    	y = d3.scale.linear().range([0,  svgheight]);
	
		var jsondata = [];
		counter=0;
		
		//#### read data from tables
		for (var j=0; j< splittables.length; j++) {
			mytotal=0;
			var mytable ='table.'+ splittables[j]+' tbody tr';
			if ($('table.'+ splittables[j]).length > 0) {
				var data = $(mytable).map(function(index) {
				    //var heads = $(this).find('th');
				    var heads = $(this).find('td:eq(4)');
				    var cols = $(this).find('td:eq(3)');
					mytotal+= parseFloat(cols[0].innerHTML);
				    return {
				        name:  heads[0].innerHTML,
				        hours: cols[0].innerHTML
				    };
				}).get();
				
			    //#### add data to json
				jsondata[counter]= ({
				    "name": splittables[j],
				    "hours": mytotal.toFixed(1),
				    "children": data
				});
				counter= counter+1;
			} //---------> if ($('table.'+ splittables[j]).length > 0)
		} //---------> for (var j=0; j< splittables.length; j++)
	

		//#### final json
		json= ({
		    "name": 'reports',
		    "children": jsondata
		});





		var treemap = d3.layout.treemap()
		    .size([svgwidth, svgheight])
		   // .sticky(true)
		    .sort(function(a,b) { return a.hours - b.hours; })
		    .round(true)
		    .value(function(d) { return d.hours; })
		   .children(function(d, depth) { 
		   		return depth ? null : d.children;
		   	})
		     // .children(function(d) { return isNaN(d.value) ? d3.entries(d.children) : null; })
		    ;
		    
		var treemap2 = d3.layout.treemap()
		    .size([svgwidth, svgheight])
		    .sort(function(a,b) { return a.hours - b.hours; })
		    .round(true)
		    .value(function(d) { return d.hours; })
		   //.children(function(d, depth) { return depth ? null : d.children; })
		     // .children(function(d) { return isNaN(d.value) ? d3.entries(d.children) : null; })			    


			//#### create chart
				var chartdiv = d3.select("#pageOutput")
				 	.insert("div", "div.clientreportlayout")
				 	.attr("class","clientreportlayout")
				 	.attr("id","svgcontainer")
				 	  .append("svg")
					      .attr("class", "chart")
						  .attr("width", svgwidth*ratio)
						  .attr("height", svgheight*ratio)
						  .style("margin-top", "1em")
						  .style("padding-bottom","3em")	
		
		        var parentCells = chartdiv
		        		.data([json])
		        				        		
		       var groupCells= parentCells.selectAll(".parentcell")
	        		.data(treemap.nodes)
	        		.enter().append("g")
	        		.attr("class", "parentcell")
		        		
		        //#### add cells
				groupCells.append("rect")
					.attr("class", function(d) { return d.name})
		    		.attr("x", function(d) { return d.x *ratio; })
			        .attr("y", function(d) { return d.y*ratio; })
			        .attr("width", function(d) { return Math.max(0, (d.dx *ratio)- 1); })
			        .attr("height", function(d) { return Math.max(0, (d.dy*ratio) - 1) ; })
			        .style("fill", function(d,i) {
								return svgcolor(d.name);
								//return "hsl(" + (data.length-i) * 7 + ",75%,75%)";
					 })
					 .style("stroke", "white")
				    .style("stroke-width", 1)
				    .on('mouseover', function(d) { 
			    		//d.depth=1; 
			    		return zoom(d);
				    });
				
				 
				 //#### add labels	 
				groupCells.append("text")
					.attr("class", "clientlabel")
		    		.attr("x", function(d) { return (d.x *ratio) +2; })
			        .attr("y", function(d) { return (d.y* ratio) +12; })
			        .attr("width", function(d) { return Math.max(0, (d.dx*ratio) - 1); })
			        .attr("height", 20)
			        .attr("text-anchor", "left") // text-align: right
					.style("font", "11px sans-serif")
					.style("font-weight", "bold")
					.style("fill", "#000")
					.text(function(d) {return d.name;  })   
					
				 //#### add hours
				groupCells.append("text")
					.attr("class", "clientlabel")
		    		.attr("x", function(d) { return (d.x *ratio) +2; })
			        .attr("y", function(d) { return (d.y* ratio) +24; })
			        .attr("width", function(d) { return Math.max(0, (d.dx*ratio) - 1); })
			        .attr("height", 20)
			        .attr("text-anchor", "left") // text-align: right
					.style("font", "11px sans-serif")
					.style("font-weight", "bold")
					.style("fill", "#000")
					.text(function(d) {return d.hours;  })   		


				//#### draw children
				function zoom(dataset) {
					
				   d3.selectAll(".childcell").remove();	
			      //var myparent= d3.select("." + dataset.name)
			      // console.log ("." + dataset.name);
			      // console.log (myparent);
			      //  myparent.remove();
			       
			       
			       console.log (dataset);
			        //console.log (dataset.children);
			       var myparentx= parseFloat(dataset.x);
			       var myratio = parseFloat(dataset.dx)/svgwidth;
			      
			       
			       var childCells = chartdiv
			        	//.data([dataset])		        		
			       var groupCells= childCells.selectAll(".childcell")
		        		//.data(treemap.nodes({value: data.children}))
		        		.data(treemap2.nodes({children: dataset.children}))
		        		.enter().append("g")
		        		.attr("class", "childcell");
			        		
			       	//d3.selectAll("rect")
				   	//    .attr('fill-opacity',.1)
				   	//    .on("click", function(d) { 
					//			$("#svgcontainer").remove();
					//		 	draw_treemap();
					//		});	 		
					//		d3.selectAll("text")
					//		.attr('fill-opacity',.1)
	 				
			        //#### add cells
					groupCells.append("rect")
			    		.attr("x", function(d) { return (d.x *myratio)+ myparentx; })
				        .attr("y", function(d) { return d.y*ratio; })
				        .attr("width", function(d) { return Math.max(0, (d.dx *myratio)- 1); })
				        .attr("height", function(d) { return Math.max(0, (d.dy*ratio) - 1) ; })
				        .style("fill", function(d,i) {
		        			//alert (d.parent.name)
		        			var mycolor =svgcolor(dataset.name);
							mycolor=d3.rgb(mycolor).darker(1)
							return d3.rgb(mycolor).brighter(2-(d.value/dataset.value*4));
							//return "hsl(" + (data.length-i) * 7 + ",75%,75%)";
						 })
						 .style("stroke", "white")
					    .style("stroke-width", 1)
					.on("click", function(d) { 
							d3.selectAll(".childcell").remove();	
						});	
						
					 //#### add labels	 
					groupCells.append("text")
						.attr("class", "clientlabel")
			    		.attr("x", function(d) { return (d.x *myratio) + myparentx+2;  })
				        .attr("y", function(d) { return (d.y* ratio) +12; })
				        .attr("width", function(d) { return Math.max(0, (d.dx*myratio) - 1); })
				        .attr("height", 20)
				        .attr("text-anchor", "left") // text-align: right
						.style("font", "11px sans-serif")
						.style("font-weight", "bold")
						.style("fill", "#000")
						.text(function(d) {return d.name;  })   
						
					groupCells.append("text")
						.attr("class", "clientlabel")
			    		.attr("x", function(d) { return (d.x*myratio) + myparentx+2; })
				        .attr("y", function(d) { return (d.y* ratio) +24; })
				        .attr("width", function(d) { return Math.max(0, (d.dx*myratio) - 1); })
				        .attr("height", 20)
				        .attr("text-anchor", "left") // text-align: right
						.style("font", "11px sans-serif")
						.style("font-weight", "bold")
						.style("fill", "#000")
						.text(function(d) {return d.hours;  })   			
				 
				}/**/	
}

