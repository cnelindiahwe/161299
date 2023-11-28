$(function(){

	// ##################  On load  ################## 


		//create chart
		drawchart();
		//create chart on window resize
		
		$(window).resize(function() {
			$(".svgcontainer, .hidedata, .showdata").remove();
		 	drawchart();
		});	


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

	

// ################## Hide monthtotals ##################
	function hidedetails ()
	{	
			d3.select("table.yeartotals ")
				.style("opacity", 0)
				.style("display", "none")
		
		 d3.select(".zowtrakui-topbar")
		 	.insert("a","a")
		 	.attr("class","showdata ")
		 	.text ("Show Data Table")
		 	
		 	.on("mousedown", function() {
				  d3.select('table.yeartotals')
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
		 	.text ("Hide Data Table")
		 	.style("display", "none")
		 	.on("mousedown", function() {
			  d3.select('table.yeartotals')
			  .style("display", "none")
			  .transition()
			  .style("opacity", 0)
			  .style("display", "none");
			  	  
			 d3.select(this)
			  .style("display", "none")
			 d3.select(".showdata")
			  .style("display", "block")
			})
	}

// ################## show month total ##################
	function drawchart ()
	{	
		
		//######### read data
		var table = d3.select("table.yeartotals"),

			datacolumns = table.selectAll("thead th")[0].map(function(d) { return d.textContent; }),
		    datarows = table.selectAll("tbody th")[0].map(function(d) { return d.textContent; });
			datacolumns.splice(0 , 1)
			
		    MaxVal=0;
		    for (var j=0; j<datacolumns.length; j++) {
		    	datacolumns[j]=datacolumns[j].replace(" ","_").replace(".","").toLowerCase()
		    	mystring=datacolumns[j];
		    	
		    	window[mystring]=  d3.selectAll("tbody td:nth-child("+(j+2)+")")[0].map(function(d) { return d.textContent.replace(",",""); });
		    	localmax=Math.max.apply( Math, window[mystring] )
		    	if (localmax>MaxVal) {MaxVal=localmax;}
				window[mystring+"max"]= localmax;
				window[mystring+"active"]= 1;
		    }

	
	//###### chart container variables	
	svgwidth=$(window).width();
	svgwheight=$(window).height()*.6;
	
	//MaxVal = parseInt(MaxVal) ;
	MaxVal = MaxVal+ (MaxVal/5);

	
	
	
	var w = ((svgwidth*.35)-40)/datarows.length;
	var h = svgwheight-40;

	  var x = d3.scale.linear()
	     .domain([0,.75])
	      .range([0, w]);
	 
	 var y = d3.scale.linear()
	      .domain([0, MaxVal])
	      .rangeRound([0, h]);
	
	 var yscale = d3.scale.linear()
	      .domain([0, MaxVal])
	      .rangeRound([h, 0]);
	
//###### create chart container
	 var chart = d3.select(".content").insert("div",".yeartotals")
		  .attr("class", "svgcontainer")
		  .style("overflow", "hidden")
	 	  .style("padding-top", "1em")
	 	  .style("margin-top", "1em")
	 	  .append("svg")
		      .attr("class", "chart")
			  .attr("width", svgwidth)
			  .attr("height", svgwheight)
			  .style("margin-top", "1em")
			  .style("padding-bottom", "3em")

//###### create chart elements



	var yAxis = d3.svg.axis()
	    .scale(yscale)
	    .orient("left")
	    .ticks(5);

	for (var j=0; j<datacolumns.length; j++) {


	  var yearbars = chart.selectAll(".yearbars"+j)
      	//.data(window[datacolumns[j]])
     	//.append("g")
        //.attr("class", "yearbars"+j)

			.data(window[datacolumns[j]])
			.enter().append("rect")
				.attr("x", function(d, i) { 
					if (i<window[datacolumns[j]].length-1) {
						return x(i)+90+((w/2)*j); 
					} else {
						return x(i-1)+90+((w/2)*j); 					
					}
				})
				.attr("y", function(d) { 	return h - y(d) - .5; })
				.attr("width", w/2)
				.attr("height", function(d) { return y(d); })
				.style("fill", function(d,i) {
					
					 if (i==window[datacolumns[j]].length-2) {
						 if (j==1) {return "LightGreen"; }
	
						 else {return "LightBlue"; }
					} else {
	
						 if (j==1) {return "YellowGreen"; }
						 else {return "steelblue"; }				
					}
				})
				.on("click", function(d,i) {
					 window.location = BaseUrl + "financials/fin_breakdown/" + (2010 +i) ;
				})


		//################## Data label	

		chart.selectAll("datalabel"+j)
			.data(window[datacolumns[j]])
			.enter().append("text")
			.attr("class", "datalabel"+j)
			.attr("x", function(d, i) {  
				if (i<window[datacolumns[j]].length-1) {
					return x(i) +90+((w/2)*j)+w/4; 
				} else {
					return x(i-1) +90+((w/2)*j)+w/4; 					
				}
			
			})
			.attr("y", function(d) { return h-y(d)-10; })  
			.attr("width", w/2 )  
			.attr("dy", ".35em") // vertical-align: middle
			.attr("text-anchor", "middle") // text-align: right
			.style("font", "11px sans-serif")
			.style("font-weight", "bold")
			.text(function(d) { 
				myvalue= commaSeparateNumber(parseInt(d));
				if (j==1) {return "$" + myvalue; }
				 else {return "\u20AC" + myvalue; }
			});     
		


	}
	
	
	//######### X axis labels


chart.selectAll("xaxislabel")
		.data(datarows)
		.enter().append("text")
		.attr("class", "xaxislabel") 
		.attr("width", w )  
		.attr("dy", ".35em") // vertical-align: middle
		.attr("text-anchor", "middle") 
		.style("font", "12px sans-serif")
		.style("fill", "#666")
		.attr ("y",function() { return h+20;})
		.attr("x", function(d, i) {	return x(i)+(w/2) +90;})
		//.attr("transform", function(d, i) { 
			//alert (x(i))
		//	return"translate(" + (x(i)+(w/2) +19.5) +"," +  (h+8) + ")rotate(270)";
		//})
		.text(function(d) { 
			return d;
		})
		//.on("click", function() {
		//	  window.location = BaseUrl + "financials/fin_breakdown/" + d;
		//}); 

chart.select(".xaxislabel:last-child").remove()

//#########Create Y axis
	padding =60;
	chart.append("g")
	    .attr("class", "yaxis")
	    .attr("transform", "translate(" + padding + ",0)")
	    .attr("fill", "none")
	    .attr("stroke", "#aaa")
	    .attr("stroke-width", 1)
	    .style ("color","#000")
	    .style("font", "10px sans-serif")
	    .call(yAxis)	

  	d3.selectAll("g text")
  		.attr("fill", "#666")
	    .attr("stroke", "none")
	
}

  function commaSeparateNumber(val){
    while (/(\d+)(\d{3})/.test(val.toString())){
      val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
    }
    return val;
  }
			
