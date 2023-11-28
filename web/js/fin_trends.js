$(function(){
	// ##################  On load  ################## 
	
		//create chart
		drawchart();
		//create chart on window resize
		
		$(window).resize(function() {
			$(".svgcontainer, .hidedata, .showdata").remove();
		 	drawchart();
		});	
		
		
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

// ##################  Create chart and plot data  ################## 
function drawchart() {
	
//load data from table	
var table = d3.select("table"),

			datacolumns = table.selectAll("thead th")[0].map(function(d) { return d.textContent; }),
		    datarows = table.selectAll("tbody th")[0].map(function(d) { return d.textContent; });
			datacolumns.splice(0 , 1)
			
		    MaxVal=0;
		    for (var j=0; j<datacolumns.length; j++) {
		    	datacolumns[j]=datacolumns[j].replace(" ","_").toLowerCase()
		    	mystring=datacolumns[j];
		    	window[mystring]=  d3.selectAll("tbody td:nth-child("+(j+2)+")")[0].map(function(d) { return d.textContent; });
		    	localmax=Math.max.apply( Math, window[mystring] )
		    	if (localmax>MaxVal) {MaxVal=localmax;}
				window[mystring+"max"]= localmax;
				window[mystring+"active"]= 1;
		    }

		
		
	
	
//###### hide table
	d3.select(".clientreportlayout ")
		.style("opacity", 0)
		.style("display", "none")

 d3.select(".zowtrakui-topbar")
 	.insert("a","a")
 	.attr("class","showdata ")
 	.text ("Show Data Table")
 	
 	.on("mousedown", function() {
		  d3.select('.clientreportlayout')
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
	  d3.select('.clientreportlayout')
	  .style("display", "none")
	  .transition()
	  .style("opacity", 0)
	  .style("display", "none");
	  	  
	 d3.select(this)
	  .style("display", "none")
	 d3.select(".showdata")
	  .style("display", "block")
	})
var chart = d3.select(".content").insert("div",".clientreportlayout")


//###### chart container variables	
	svgwidth=$(window).width();
	svgwheight=$(window).height()*.6;
	
	//MaxVal = parseInt(MaxVal) ;
	MaxVal = MaxVal+ (MaxVal/5);

	
	
	
	var w = ((svgwidth*.75)-40)/datarows.length;
	var h = svgwheight-40;
	  
	  var x = d3.scale.linear()
	     .domain([0, 1])
	      .range([0, w]);
	 
	 var y = d3.scale.linear()
	      .domain([0, MaxVal])
	      .rangeRound([0, h]);
	
	 var yscale = d3.scale.linear()
	      .domain([0, MaxVal])
	      .rangeRound([h, 0]);
	
//###### create chart container
	 var chart = d3.select(".content").insert("div",".clientreportlayout")
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
	var line = d3.svg.line()
	    .x(function(d,i) { 
	    	return x(i)+(w/2) +19.5; 
	    	})
	    .y(function(d) { return h-y(d); })


	var yAxis = d3.svg.axis()
	    .scale(yscale)
	    .orient("left")
	    .ticks(5);
	    
	    
	    
//######### year lines

	var xyear=[3,15,27,39,51,63,75,87,99] //,111,123,135,147,159,171,183];

	for (var yearcount=0; yearcount<xyear.length; yearcount++) {
		xyearval=x(xyear[yearcount])+(w/2) +19.5;	
		chart.append("svg:line")
		    .attr("x1", xyearval)
		    .attr("y1", 0)
		    .attr("x2", xyearval)
		    .attr("y2", h)
		    .style("stroke", "#999")
		    .style("stroke-width", 1)
		    .style("stroke-dasharray", "3, 2")
   }
        
//######### graph lines 
 
 
	mycolors = ["steelblue","#99CC32", "#D02090","#FF7F00","#66CDAA","#967117","#FE6F5E","#D2691E","#FFEF00","#BF4F51"];     
	   
	for (var j=0; j<datacolumns.length; j++) {



	 	//##### add trend line
		    var linreg = linearregression();
			linreg.data(window[datacolumns[j]]);
		    var trend= chart.append('path')
		        .attr('class', 'linreg')
		        .attr('id', 'trendline'+j)
				.attr('d', function() { return 'M' + linreg.path(); })
        		.attr("stroke", "#dddddd")
        		.attr("stroke-width", 3)
				.attr("opacity", 0)


		//##### add graph line
		chart.append("svg:path")
			.attr("class", "lineplot plotline"+j)
			.attr ("id",datacolumns[j])
			.attr("d", line(window[datacolumns[j]]))
			.attr("stroke", mycolors[j])
			.attr("stroke-width", 3)
			.attr("fill", "none")
	
		   	
		   	 
		   	 /*.on("mouseover", function() {
		   		  thisclass=d3.select(this).attr("class")
		   		  thisseries=thisclass.substr(-1);
				  d3.selectAll('.lineplot:not(:hover)').transition() 
				          .attr("opacity", 0.2)
				          .duration(500) ;
				  
				  d3.selectAll('.point'+thisseries).transition() 
				          .attr("opacity", 1)
				          .duration(500) ;
				          
				  d3.selectAll('.datalabel'+thisseries).transition() 
				          .attr("opacity", 1)
				          .duration(500) ;
		      })
	             
			
		   	.on("mouseout", function() { 
				  d3.selectAll('.lineplot').transition() 
				          .attr("opacity",1)
				          .duration(250) ;
				          
				  thisseries=d3.select(this).attr("class").substr(-1)
				  d3.selectAll('.point'+thisseries).transition() 
				          .attr("opacity", 0)
				          .duration(250) ;
				          
				  d3.selectAll('.datalabel'+thisseries).transition() 
				          .attr("opacity", 0)
				          .duration(250) ;
	       })
	      */
	       	//##### add line title
	       	.append("svg:title")
	          .text(function(d, i) { 
	          	titletext=datacolumns[j];
	          	if (titletext.substring(0,3)=="new"){
	          		titletext=titletext.substring(0,3)+" "+titletext.slice(3);
	          	}          	
	          	return titletext.charAt(0).toUpperCase() + titletext.slice(1);
	
	          });
	     
	 
	      	
		//################## Data dots	

	chart.selectAll('.point'+j)
			.data(window[datacolumns[j]])
			.enter().append("svg:circle")
		   .attr("class", "point"+j)
		   .attr("cx", function(d,i) { return x(i)+(w/2) +19.5;})
		   .attr("cy",function(d) { return h-y(d); } )  
		   .attr("r", 5)
		   .attr("opacity", 0)
	
		  
		//################## Data label	

		chart.selectAll("datalabel"+j)
			.data(window[datacolumns[j]])
			.enter().append("text")
			.attr("class", "datalabel"+j)
			.attr("x", function(d, i) { return x(i)+(w/2) +19.5 ; })
			.attr("y", function(d) { return h-y(d)-15; })  
			.attr("width", w )  
			.attr("dy", ".35em") // vertical-align: middle
			.attr("text-anchor", "middle") // text-align: right
			.style("font", "10px sans-serif")
			.text(function(d) { return d; })
			.attr("opacity", 0);     
		}

  
//######### X axis labels


	chart.selectAll("xaxislabel")
		.data(datarows)
		.enter().append("text")
		.attr("class", "xaxislabel") 
		.attr("width", w )  
		.attr("dy", ".35em") // vertical-align: middle
		.attr("text-anchor", "end") 
		.style("font", "10px sans-serif")
		.style("fill", "#666")
		//.attr ("y",function() { return h-10;})
		//.attr("x", function(d, i) {  
		//	return x(i);
		//})
		.attr("transform", function(d, i) { 
			//alert (x(i))
			return"translate(" + (x(i)+(w/2) +19.5) +"," +  (h+8) + ")rotate(270)";
		})
		.text(function(d) { 
			return d;
		}); 



//#########Create Y axis
	padding =30;
	chart.append("g")
	    .attr("class", "yaxis")
	    .attr("transform", "translate(" + padding + ",0)")
	    .attr("fill", "none")
	    .attr("stroke", "#aaa")
	    .attr("stroke-width", 1)
	    .style ("color","#000")
	    .style ("Font-size","10px")
	    .call(yAxis)	

  	d3.selectAll("g text")
  		.attr("fill", "#666")
	    .attr("stroke", "none")
	    
	    
	    
//#########add legend  


	var legendx=svgwidth*.75;
	 var legend = 	chart.append("g")
	  .attr("class", "legend")
	  .attr("x", legendx)
	  .attr("y", 25)
	  .attr("height", 100)
	  .attr("width", 100);

	legend.selectAll('g').data(datacolumns)
      .enter()
      .append('g')
      .each(function(d, i) {
      	//######### legend markers
        var g = d3.select(this);
        g.append("rect")
           .attr("class","legendmarker"+i)
          .attr("x",legendx)
          .attr("y", i*25)
          .attr("width", 15)
          .attr("height", 15)
          .style("fill", mycolors[i])
          .style("stroke", mycolors[i])
          .style("stroke-width", 1)
          
          .on("mouseover", function() {
          		backc= d3.select(this).style('fill');
          		if (backc== "#ffffff") { return false;}
		   		  thisclass=d3.select(this).attr("class")
		   		  thisseries=thisclass.substr(-1);
				 d3.selectAll('.lineplot:not(.plotline'+i+')').transition() 
				          .attr("opacity", 0.2)
				          //.duration(500) ;
				  
				  d3.selectAll('.point'+thisseries).transition() 
				          .attr("opacity", 1)
				          //.duration(500) ;
				          
				  d3.selectAll('.datalabel'+thisseries).transition() 
				          .attr("opacity", 1)
				          //.duration(500) ;
				  d3.selectAll('#trendline'+thisseries).transition() 
				          .attr("opacity", 1)
				          //.duration(500) ;

		      })
          
  		   	.on("mouseout", function() { 
				  d3.selectAll('.lineplot').transition() 
				          .attr("opacity",1)
				          //.duration(250) ;
				          
				  thisseries=d3.select(this).attr("class").substr(-1)
				  d3.selectAll('.point'+thisseries).transition() 
				          .attr("opacity", 0)
				          //.duration(250) ;
				          
				  d3.selectAll('.datalabel'+thisseries).transition() 
				          .attr("opacity", 0)
				          //.duration(250) ;
				  d3.selectAll('#trendline'+thisseries).transition() 
				          .attr("opacity", 0)
				          //.duration(500) ;
	       }) 

  		   	.on("mousedown", function() {
  		   		thisseries=d3.select(this).attr("class").substr(-1)
  		   		 backc= d3.select(this).style('fill');
  		   		 if (backc== "rgb(255, 255, 255)") {
				   d3.select(this)
				  	 .style("fill", mycolors[thisseries]);
				  	 
				   d3.selectAll('.plotline'+thisseries)
				     	  .style("display","block");
				     d3.selectAll('.datalabel'+thisseries)
				     	  .style("display","block");	
				     d3.selectAll('.point'+thisseries)
				     	  .style("display","block");
				  d3.selectAll('#trendline'+thisseries)
				          .style("display","block	");		
				    d3.selectAll('.lineplot:not(.plotline'+i+')').transition() 
				          .attr("opacity", 0.2)
				          //.duration(250) ;
				   d3.selectAll('.point'+thisseries).transition() 
				          .attr("opacity", 1)
				          //.duration(500) ;
				  d3.selectAll('.datalabel'+thisseries).transition() 
				          .attr("opacity", 1)
				          //.duration(500) ;
				  d3.selectAll('.trendline'+thisseries).transition() 
				          .attr("opacity", 1)
				          //.duration(500) ;
				 	lineid=d3.select('.plotline'+thisseries).attr("id");
				 	window[lineid+"active"]= 1;
				    scaleyaxis();  
				 }
				 else {
				 	 d3.selectAll('.lineplot').attr("opacity",1)
				 	 d3.select(this)
				   	.style("fill", "#ffffff");
				     d3.selectAll('.plotline'+thisseries)
				     	  .style("display","none");			   	
				     d3.selectAll('.datalabel'+thisseries)
				     	  .style("display","none");	
				     d3.selectAll('.point'+thisseries)
				     	  .style("display","none");	
				  d3.selectAll('#trendline'+thisseries)
				          .style("display","none");				     
			          
				     lineid=d3.select('.plotline'+thisseries).attr("id");
				     window[lineid+"active"]= 0;
				     scaleyaxis();      

				 }  		   		 	
	       }) 	       
	             
        //######### legend text
        g.append("text")
			.attr("class","legendtext"+i)
          .attr("x", legendx+20)
          .attr("y", i * 25 + 13)
          .attr("height",30)
          .attr("width",100)
          .style("fill", "#333")
           .style ("Font-size","11px")
          .text(function(d) { 
          	titletext=d;
	          	if (titletext.substring(0,3)=="new"){
	          		titletext=titletext.substring(0,3)+" "+titletext.slice(3);
	          	}          	
	          	return titletext.charAt(0).toUpperCase() + titletext.slice(1);
			return d;
			})
			.on("mouseover", function() {
		   		  thisclass=d3.select(this).attr("class")
		   		  thisseries=thisclass.substr(-1);
				  d3.selectAll('.lineplot:not(.plotline'+i+')').transition() 
				          .attr("opacity", 0.2)
				          //.duration(500) ;
				  
				  d3.selectAll('.point'+thisseries).transition() 
				          .attr("opacity", 1)
				          //.duration(500) ;
				          
				  d3.selectAll('.datalabel'+thisseries).transition() 
				          .attr("opacity", 1)
				          //.duration(500) ;
		      })
	             
			
		   	.on("mouseout", function() { 
				  d3.selectAll('.lineplot').transition() 
				          .attr("opacity",1)
				          //.duration(250) ;
				          
				  thisseries=d3.select(this).attr("class").substr(-1)
				  d3.selectAll('.point'+thisseries).transition() 
				          .attr("opacity", 0)
				          //.duration(250) ;
				          
				  d3.selectAll('.datalabel'+thisseries).transition() 
				          .attr("opacity", 0)
				          //.duration(250) ;
	       }) 
		
	  });   
	function scaleyaxis(){
		mystring=0;
		MaxVal=0;
		for (var j=0; j<datacolumns.length; j++) {
			mystring=datacolumns[j];
		    	if (window[mystring+"active"]!=0) {
		    		if (window[mystring+"max"] > MaxVal) {
		    			MaxVal=window[mystring+"max"];
		    			
		    		}
		    	}
		 }	
		 MaxVal=MaxVal+(MaxVal*.2);
	   	yscale.domain([0,MaxVal]) 

	      	   	
   		chart.select(".yaxis")
            .transition().duration(500).ease("sin-in-out")  // https://github.com/mbostock/d3/wiki/Transitions#wiki-d3_ease
            .call(yAxis);    
             
		  y = d3.scale.linear()
		      .domain([0, MaxVal])
		      .rangeRound([0, h]);	
		      
		for (var j=0; j<datacolumns.length; j++) {
			chart.select(".plotline"+j)
				//.transition().duration(500).ease("sin-in-out")
				.transition()
				.attr("d", line(window[datacolumns[j]]))
				
			//##### trend line
			var linreg = linearregression();
			linreg.data(window[datacolumns[j]]);
			chart.select("#trendline"+j)
				.transition()
				.attr('d', function() { return 'M' + linreg.path(); })
        		.attr("stroke", "#aaaaaa")	
				
			chart.selectAll(".datalabel"+j)
				.transition()
				.attr("y", function(d) { return h-y(d)-15; }) 
			chart.selectAll('.point'+j)
						.transition()
					   .attr("cy",function(d) { return h-y(d); } )  

		}

	



	}

	// # Linear Regression
	// [Simple linear regression](http://en.wikipedia.org/wiki/Simple_linear_regression)
	// is a simple way to find a fitted line
	// between a set of coordinates.
	// https://gist.github.com/tmcw/2879363
function linearregression() {
	

	 
    var linreg = {},
        data = [];
        
	 
	      
    linreg.data = function(xdat) {
        if (!arguments.length) return data;
        
        datat = xdat.slice();
        for (var i = 0; i < datat.length; i++) {
         	data.push([x(i+1),Math.round(datat[i])]);
         	
         }
       data.sort(function(a, b) { return a[0] - b[0]; });


        return linreg;
    };

    linreg.path = function() {

        var sum_x = 0, sum_y = 0,
            sum_xx = 0, sum_xy = 0,
            m, b;

        for (var i = 0; i < data.length; i++) {
            sum_x += data[i][0];
            sum_y += data[i][1];


            sum_xx += Math.pow(data[i][0], 2);
            sum_xy += data[i][0] * data[i][1];
        }

        m = (data.length * sum_xy - sum_x * sum_y) /
            (data.length * sum_xx - sum_x * sum_x);
        b = (sum_y / data.length) - (m * sum_x) / data.length;

        return [
            [data[0][0], h-y(data[0][0] * m + b)],
            [data[data.length - 1][0], h-y(data[data.length - 1][0] * m + b)]
        ];
    };

    return linreg;
}

};




