$(function(){
	
	//client dropdown
	$('#client_dropdown_selector_submit').hide();
	$("#client_dropdown_selector").change ( function () {
			$("#client_dropdown_form").submit();
		}
	);
	
	// originator dropdown
	$('#contact_dropdown_selector_submit').hide();
	$("#contact_dropdown_selector").change ( function () {
			$("#contact_dropdown_form").submit();
		}
	);
	
	draw_momentum_chart();
	

	$('.panel-heading').append('<a href="" id="ButtonHours" class="ChartButton btn btn-success btn-xs">Hours Billed</a>');
	$('.panel-heading').append('<a href="" id="ButtonJobs" class="ChartButton btn btn-default btn-xs">Jobs</a>');
	$('.panel-heading').append('<a href="" id="ButtonHoursJob" class="ChartButton btn btn-default btn-xs">Hours per Job</a>');
	$(".panel-title").append('<span>Hours billed</span>');
	
	$('.ChartButton').click(function() {
		$( ".ChartButton" ).removeClass( "btn-success" ).addClass( "btn-default" );
		$(this).removeClass( "btn-default" ).addClass(  "btn-success");
		$("#chart-area svg , .panel-title span" ).remove();
		return false;
	});
	
	$('#ButtonHours').click(function() {
		draw_momentum_chart(1);
		$(".panel-title").append('<span>Hours billed</span>');
	});
	
	$('#ButtonJobs').click(function() {
		draw_momentum_chart(2);
		$(".panel-title").append('<span>Number of Jobs</span>');		
	});

	$('#ButtonHoursJob').click(function() {
		draw_momentum_chart(3);
		$(".panel-title").append('<span>Average Hours per Job</span>');		
	});
	
	/*$( window ).resize(function() {
		alert("hey");
		$("#chart-area svg").remove();
		draw_momentum_chart();
	});
	

	*/
});   //$(function()     



function draw_momentum_chart (ChoiceColumn=1) {
	
	$(".tooltip" ).remove();
	
	var tooltip_div = d3.select("#chart-area").append("div")
    .attr("class", "tooltip")
    .style("opacity", 0);

	
	
	// set the dimensions and margins of the graph
	var margin = {top:40,
		bottom:40,
		right: $( document ).width()*.05,
		left: $(  document ).width()*.05 },
		svgwidth = $("#chart-area").width()-90, //60 = fixed left and right page margins
		svgheight  = $("#annualdatatable").height()-60;


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
	
	var mytable ='#annualdatatable tbody tr';
	
	var data = $(mytable).map(function(index) {
		var cols = $(this).find('td');

		return {
			date:cols[0].innerText,
			billedhours:cols[ChoiceColumn].innerText.split(',').join('')
		}
	}).get();

	// console.log(data);	
	
	
	linregdata = data.map(function(d,i) { return i, d.billedhours; });

	//console.log(linregdata);	


	  // format the data
  data.forEach(function(d) {
    d.billedhours = +d.billedhours;
  });
	

	
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
	  .style("fill","#557AAB") 
	  
	;

  //## append bar labels

		svg.selectAll(".bartext")
		.data(data)
		.enter()
		.append("text")
		.attr("class", "barlabel")
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
	  



}




    



