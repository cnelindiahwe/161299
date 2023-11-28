$(function(){

	// ##################  On load  ################## 

	automateform()
			// ##################  Row highlight on hover ################## 
	rowhighlight();

	drawtreemap();

// ##################  End $(function(){  ################## 
});

// ################## automate form ##################

	 	function  rowhighlight(){
				$('tr').hover( 
					  function () {
						$(this).children('td').siblings().addClass("rowhighlight");
					  },
					  function () {
						$(this).children('td').siblings().removeClass("rowhighlight");
					  }
				);
		}



// ################## automate form ##################

	 	function  automateform(){
			$("#yearsubmit").hide();
			$("#year").change ( function () {
					$("#yearcontrol").submit();
				}
			);
		}

// ################## draw chart ##################
function drawtreemap () {
		
		//#### Hide tables
			d3.selectAll("table ")
				.style("opacity", 0)
				.style("display", "none")
				
				 d3.select(".zowtrakui-topbar")
				 	.insert("a","a")
				 	.attr("class","showdata ")
				 	.text ("Show Data Tables")
				 	
				 	.on("mousedown", function() {
						  d3.selectAll('table')
						  .style("display", "block")
						  .transition()
						  .style("opacity", 1);
					  
						 d3.select(this)
						  .style("display", "none")
						 d3.select(".hidedata")
						  .style("display", "block")
					})
					
				 d3.select(".zowtrakui-topbar ")
				 	.insert("a","a")
				 	.attr("class","hidedata")
				 	.text ("Hide Data Tables")
				 	.style("display", "none")
				 	.on("mousedown", function() {
					  d3.selectAll('table')
					  .style("display", "none")
					  .transition()
					  .style("opacity", 0)
					  .style("display", "none");
					  	  
					 d3.select(this)
					  .style("display", "none")
					 d3.select(".showdata")
					  .style("display", "block")
					})				
		//#### set dimensions	
		var svgwidth = $(window).width()-($(window).width()*.05),
	    svgheight = $(window).height()-($(window).height()*.35),
	    vgcolor = d3.scale.category20c();

		var eurototal =0,
		usdototal =0;
		
		
		for (var j=0; j<2; j++) {
				mytotal = 0;
				//#### read data
				if (j==0) {		
					var mytable ='table#eurclienttotals tbody tr';
				} else {
					var mytable ='table#usdclienttotals tbody tr';
				}
				var data = $(mytable).map(function(index) {
				    var heads = $(this).find('th');
				    var cols = $(this).find('td');
						mytotal+= parseFloat(cols[0].innerHTML);

				    return {
				        client:  heads[0].innerHTML,
				        revenue: cols[0].innerHTML
				    };
				}).get();


				//#### add totals to titles
				if (j==0) {		
					eurototal= mytotal;
					var mytitle =".eurotitle";
					var cursymbol ="\u20AC";
				} else {
					var mytitle=".usdtitle";
					var cursymbol ="$";
					usdtotal = mytotal;
				}
				
			d3.select(mytitle)
			.insert ("span")
			.text(" " + cursymbol + addCommas(parseInt(mytotal)));	
		
			//#### convert data to json
				var json = {
				    "name": "tags",
				    "children": data
				};


			
	var treemap = d3.layout.treemap()
		    .size([svgwidth, svgheight])
		    .sticky(true)
		    .sort(function(a,b) { return a.revenue - b.revenue; })
		    .round(true)
		    .value(function(d) { return d.revenue; });
		
		if (j==0) {
			insertname="#eurclienttotals";
			chartid="eurtreemap";
			ratio=1
		} else {
			insertname="#usdclienttotals";
			chartid="usdtreemap";
			ratio=Math.sqrt(usdtotal/eurototal);
		}
			
			
			//#### create chart
			var chartdiv = d3.select(".content")
					 	.insert("div",insertname)
					 	.attr("id",chartid)
					 	  .append("svg")
						      .attr("class", "chart")
							  .attr("width", svgwidth*ratio)
							  .attr("height", svgheight*ratio)
							  .style("margin-top", "1em")
							  .style("padding-bottom","3em")	
	
	        var parentCells = chartdiv
	        		.data([json])
	       var groupCells= parentCells.selectAll(".cell")
	        		.data(treemap.nodes)
	        		.enter().append("g")
	        		
	        //#### add cells
			groupCells.append("rect")
	    		.attr("x", function(d) { return d.x *ratio; })
		        .attr("y", function(d) { return d.y*ratio; })
		        .attr("width", function(d) { return Math.max(0, (d.dx *ratio)- 1); })
		        .attr("height", function(d) { return Math.max(0, (d.dy*ratio) - 1) ; })
		        .style("fill", function(d,i) {
							//return svgcolor(d.revenue);
							return "hsl(" + (data.length-i) * 7 + ",75%,75%)";
				 })
				 .style("stroke", "white")
			    .style("stroke-width", 1)
				 
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
				.text(function(d) {return d.client;  })
				
			groupCells.append("text")
				.attr("class", "datalabel")
	    		.attr("x", function(d) { return (d.x*ratio) +2; })
		        .attr("y", function(d) { return (d.y*ratio)+24; })
		        .attr("width", function(d) { return Math.max(0, (d.dx*ratio) - 1); })
		        .attr("height", 20)
		        .attr("text-anchor", "left") // text-align: right
				.style("font", "11px sans-serif")
				.style("font-weight", "bold")
				.style("fill", "#000")
				.text(function(d) {
					myvalue= addCommas(parseInt(d.value));
					if (j==1) {
						return "$" + myvalue + " (" + parseInt(d.value/usdtotal*100) + "%)";
						}
					 else {
					 	return "\u20AC" + myvalue + " (" + parseInt(d.value/eurototal*100) + "%)";;
					    }
					return d.value; 
				})
 
 	}
	function addCommas(nStr)
	{
		nStr += '';
		x = nStr.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;
	}	 
}