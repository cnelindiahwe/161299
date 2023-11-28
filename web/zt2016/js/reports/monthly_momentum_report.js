$(function(){
	
	// month dropdown
	$('#month_dropdown_selector_submit').hide();
	$("#month_dropdown_selector").change ( function () {
			$("#month_dropdown_form").submit();
		}
	);


	draw_momentum_chart();
	
	$( window ).resize(function() {
		
		$("#chart-area svg").remove();
		draw_momentum_chart();
	});
	
});   //$(function()     



function draw_momentum_chart () {
	
	$(".tooltip" ).remove();
	
	var tooltip_div = d3.select("#chart-area").append("div")
    .attr("class", "tooltip")
    .style("opacity", 0);

	
	
	// set the dimensions and margins of the graph
	var margin = {top:40,
		bottom:40,
		right: $( document ).width()*.05,
		left: $(  document ).width()*.05 },
		svgwidth = $(  document ).width()-90, //60 = fixed left and right page margins
		svgheight  = $( window ).height()/2;


	// set the ranges
	var x = d3.scaleBand()
			  .range([0, svgwidth-1])
			  .padding(0.1);
	var y = d3.scaleLinear()
			  .range([svgheight, 0]);

	// append the svg object to the body of the page
	// append a 'group' element to 'svg'
	// moves the 'group' element to the top left margin
	var svg = d3.select("#chart-area").append("svg")
		.attr("width", svgwidth)
		.attr("height", svgheight + margin.top + margin.bottom)

	//.style("background-color","aqua")

    .append("g")
    .attr("transform", 
	   "translate(0," + margin.top + ")");

	// get the data from the table
	
	var mytable ='#days-table tbody tr';
	
	var data = $(mytable).map(function(index) {
		var cols = $(this).find('td');

		return {
			date:cols[0].innerText.substring(0,6),
			billedhours:cols[1].innerText,
		    jobs:cols[2].innerText,
		    new:cols[3].innerText,
		    edits:cols[4].innerText,
		    hours:cols[5].innerText,
		}
	}).get();

	// console.log(data);	
	
	
	linregdata = data.map(function(d,i) { return i, d.billedhours; });

	//console.log(linregdata);	


	  // format the data
  data.forEach(function(d) {
    d.billedhours = +d.billedhours;
  });
	


var colorScale = d3.scaleLinear().domain([0, d3.max(data, function(d) { return d.billedhours; })])
      .interpolate(d3.interpolateHcl)
      .range([d3.rgb("#C00000"), d3.rgb('#00B050')]);
	
	
  // Scale the range of the data in the domains
  x.domain(data.map(function(d) { return d.date; }));
  y.domain([0, d3.max(data, function(d) { return d.billedhours; })]);

  //## append the rectangles for the bar chart
  svg.selectAll(".bar")
      .data(data)
      .enter()
	  .append("rect")
      .attr("class", "bar")
      .attr("x", function(d) { return x(d.date); })
      .attr("width", x.bandwidth())
      .attr("y", function(d) { return y(d.billedhours); })
      .attr("height", function(d) { return svgheight - y(d.billedhours); })
	  .style("fill", function(d) {
    	return colorScale(d.billedhours);
  		})
	  
	  .on("mouseover", function(event,d) {
       tooltip_div.transition()
         .duration(50)
         .style("opacity", .95);
       tooltip_div.html( "<ul class=\"list-group\"><li class=\"list-group-item active\">"+ d.date +"</li><li class=\"list-group-item\">" + d.billedhours + " billed hours</li><li class=\"list-group-item\">" + d.jobs + " jobs</li><li class=\"list-group-item\">" + d.new + " new slides</li><li class=\"list-group-item\">"+ d.edits + " edited slides</li><li class=\"list-group-item\">"+ d.hours + " hours</li></ul>")
        // .style("left",  svgwidth/2 + "px")
        //	 .style("top",  "170px");
       })
	
      .on("mouseout", function(d) {
       tooltip_div.transition()
         .duration(50)
         .style("opacity", 0);
       })
	;
	


  //## append bar labels

		svg.selectAll(".bartext")
		.data(data)
		.enter()
		.append("text")
		.attr("class", "bartext")
		.attr("text-anchor", "middle")
		.attr("width", x.bandwidth())
		.attr("x", function(d,i) {
			return x(d.date)+x.bandwidth()/2; // centers the label horizontally
		})
		.attr("y", function(d,i) {
			return y(d.billedhours)-10;
		})
		.text(function(d){
			 return d.billedhours;
		});
	
  // add the x Axis
/*  svg.append("g")
      .attr("transform", "translate(0," + svgheight + ")")
      .call(d3.axisBottom(x))*/

  svg.append("g")
      .attr("transform", "translate(0," + svgheight + ")")
      .call(d3.axisBottom(x))
	
          //.call(_config.xAxisGen)
          .selectAll('.x .tick text') // select all the x tick texts
          .call(function(t){                
            t.each(function(d){ // for each one
              var self = d3.select(this);
              var s = self.text().split(' ');  // get the text and split it
              self.text(''); // clear it out
              self.append("tspan") // insert two tspans
                .attr("x", 0)
                .attr("dy",".8em")
                .text(s[0]);
              self.append("tspan")
                .attr("x", 0)
                .attr("dy",".8em")
                .text(s[1]);
            })
	
  })
	  
	
	
  // add the average line
	
	var dataSum = d3.sum(data, function(d) {
		return d.billedhours;
	});
	
	var hoursavbg=$("#hoursavg").text();
	
	svg.append("line")
		.attr("x1", 0)
		.attr("x2",  svgwidth)
		//.attr("y1",y(dataSum / data.length))
		//.attr("y2", y(dataSum / data.length))
		.attr("y1",y(hoursavbg))
		.attr("y2",y(hoursavbg))
		.attr("stroke", "#999")
		.attr("stroke-width", 1)
		.attr("stroke-dasharray","8,5")

	

	
	
	
		/*var linreg = linearregression();
		linreg.data(linregdata );
		var trend= svg.append('path')
			.attr('class', 'linreg')
			.attr('id', 'trendline')
			.attr('d', function() {linreg.path(); })
			.attr("stroke", "#dddddd")
			.attr("stroke-width", 3)
			.attr("opacity", 0)	
*/


  // add the y Axis
  //svg.append("g")
   //   .call(d3.axisLeft(y));

//});


}




// # Linear Regression
// [Simple linear regression](http://en.wikipedia.org/wiki/Simple_linear_regression)
// is a simple way to find a fitted line
// between a set of coordinates.
function linearregression() {
    var linreg = {},
        data = [];

    /* linreg.data = function(x) {
        if (!arguments.length) return data;
        data = x.slice();
   		//console.log (data);
        data.sort(function(a, b) { return a[0] - b[0]; });
        return linreg;
    };
*/
    linreg.data = function(xdat) {
        if (!arguments.length) return data;
        
        datat = xdat.slice();
        for (var i = 0; i < datat.length; i++) {
         	data.push([(i+1),Math.round(datat[i])]);         	
         }
       data.sort(function(a, b) { return a[0] - b[0]; });

		return linreg.data;	
	};
	
	//str = JSON.stringify (linreg, null, 4); // (Optional) beautiful indented output.
	//alert(str); 
	
    linreg.path = function() {
        var sum_x = 0, sum_y = 0,
            sum_xx = 0, sum_xy = 0,
            m, b;

        for (var i = 0; i < data.length; i++) {
            sum_x += data[i][0];
            sum_y += data[i][0];

            sum_xx += Math.pow(data[i][0], 2);
            sum_xy += data[i][0] * data[i][1];
        }

        m = (data.length * sum_xy - sum_x * sum_y) /
            (data.length * sum_xx - sum_x * sum_x);
        b = (sum_y / data.length) - (m * sum_x) / data.length;

        return [
            [data[0][0], data[0][0] * m + b],
            [data[data.length - 1][0], data[data.length - 1][0] * m + b]
        ];
    };
		//alert (linreg);
	

    return linreg;
}

    



