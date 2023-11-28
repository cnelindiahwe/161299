$(function(){
	
	
	
	
	// month dropdown
	$('#date_dropdown_selector_submit').hide();
	$("#date_dropdown_selector").change ( function () {
			$("#date_dropdown_form").submit();
		}
	);

	//Table
	$('#breakdown-table').DataTable({
		"language": {
		  "info": "_TOTAL_ client(s)",
		  "infoFiltered": " <small>out of _MAX_ total monthly client(s)</small>"
		},
		"dom":'<"top"i><"clear">',
		"order": [ 2, "desc" ],
		paging: false,
		info: true,
		"columnDefs": [
			{"targets": [0,1], "orderable": false},
			//{ orderable: false, targets: 'no-sort'},
		 ],
		
		/*BELOWFROM HERE:
		 * https://datatables.net/examples/api/multi_filter_select.html*/
		initComplete: function () {

			this.api().columns([0]).every( function () {
				var column = this;				
				var select = $('<select><option value=""></option></select>')
				   .appendTo( $(column.footer()).empty() )
				   .on( 'change', function () {

						var val = $.fn.dataTable.util.escapeRegex( $(this).val());
						column
							.search( val ? '.*'+val+'.*' : '', true, false )
							.draw();
					  
					   // Reset the other dropdown
					   // if (column[0]==0) {var selcol=2} else {var selcol=1};
					   // $("#clients-table tfoot tr th:nth-child("+selcol+") select").val("");
					   
					   
				   } ); //.on( 'change', function ()

				var ExistingItems= [];
				column.data().unique().sort().each( function ( d, j ) {
					if (d.substring(0, 7)=="<a href" || d.substring(0, 7)=="<small>") {
						//http://stackoverflow.com/questions/960156/regex-in-javascript-to-remove-links
						d=d.replace(/<a\b[^>]*>/i,"").replace(/<\/a>/i, "");
						d=d.replace(/<small>/i, "").replace(/<a\b[^>]*>/i,"").replace(/<\/a>/i, "").replace(/<\/small>/i, "");
						}
					//https://stackoverflow.com/questions/5864408/javascript-is-in-array
					if( ExistingItems.indexOf(d) < 0)
					  {  
						 ExistingItems.push(d);
						select.append( '<option value="'+d+'">'+d+'</option>' );
					  }
				} );
			} ); //this.api().columns([0,1]).every


		} , //initComplete: 

         "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Totals
           var BilledHoursTotal = api
                .column( 2, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 

           var JobsTotal = api
                .column( 3, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );		
			 
          var NewTotal = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );			 
			 
          var EditsTotal = api
                .column( 5, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );	
			 
          var HoursTotal = api
                .column( 6, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );	
			 
            // Update footer
            $( api.column(2).footer() ).html(
                BilledHoursTotal.formatdecimals(2)  //+ ' ('+ total.formatMoney(2) +')'
            );
            $( api.column(3).footer() ).html(
                JobsTotal  //+ ' ('+ total.formatMoney(2) +')'
            );
            $( api.column(4).footer() ).html(
                NewTotal  //+ ' ('+ total.formatMoney(2) +')'
            );
            $( api.column(5).footer() ).html(
               EditsTotal  //+ ' ('+ total.formatMoney(2) +')'
            );
            $( api.column(6).footer() ).html(
               HoursTotal.formatdecimals(2)  //+ ' ('+ total.formatMoney(2) +')'
            );
		 }
		
	});
	
	//$('#example_filter').detach().prependTo('.container')
	$('#breakdown-table-title H4').empty();
	$('#breakdown-table_info').detach().prependTo('#breakdown-table-title H4')
	
	
	drawtreemap();
	
	
	$('#breakdown-table').on( 'order.dt',  function () { 
		$('#client-totals-div svg').remove();
		drawtreemap();
	} )
		

	
});   //$(function()     




function drawtreemap () {
	
	if( $("#breakdown-table").text().includes("No matching records found")) { return;}
	
	// set the dimensions and margins of the graph
	var margin = {top:0, right: 0, bottom:0, left: 0},
		svgwidth = $( document ).width()*.95,
		svgheight  = $( window ).height()*.50;

	// append the svg object to the body of the page
	var svg = d3.select('#client-totals-div')
		.append("svg")
		  .attr("width", svgwidth + margin.left + margin.right)
		  .attr("height", svgheight + margin.top + margin.bottom)
		.lower() //prepend
		.append("g")
		  .attr("transform",
				"translate(" + margin.left + "," + margin.top + ")");


	// Read data


	
	$('#breakdown-table tbody tr:first').before('<tr><td></td> <td>Month</td> <td></td> <td></td> <td></td> <td></td> <td></td></tr>');
	
	//alert ($("#clients-table").text());
	
	
	var mytable ='#breakdown-table tbody tr';
	
	
	
	var data = $(mytable).map(function(index) {
		var cols = $(this).find('td');
		if (index==0){
			return {
			originator:'Month',
			root:"",
			hours:""
			}
		} else {
			return {
			//originator:cols[2].innerText,
			group:cols[0].innerText,
			clientcode:cols[1].attributes['data-clientcode'].value,
			client:cols[1].innerText,
			root:'Month',
			billedhours:cols[2].innerHTML.replace(/,/g, ''),
			jobs:cols[3].innerHTML.replace(/,/g, ''),
			newslides:cols[4].innerHTML.replace(/,/g, ''),
			editedslides:cols[5].innerHTML.replace(/,/g, ''),
			additionalhours:cols[6].innerHTML.replace(/,/g, ''),
			}					
		}
	}).get();

	console.log(data);


	//#### convert data to json
	var json = {
		"name": "tags",
		"children": data
	};			
		
	
	$('#breakdown-table tbody tr:first-child').remove();

	  // stratify the data: reformatting for d3.js
	  var root = d3.stratify()
		//.id(function(d) { return d.originator; })   // Name of the entity (column name is name in csv)
	    .id(function(d) { return d.originator; })   // Name of the entity (column name is name in csv)
		.parentId(function(d) { return d.root; })   // Name of the parent (column name is parent in csv)
		(data);
	
	  //console.log("h"&root);
	
	  root.sum(function(d) { return +d.billedhours })   // Compute the numeric value for each entity

	  // Then d3.treemap computes the position of each element of the hierarchy
	  // The coordinates are added to the root object above
	  d3.treemap()
		.size([svgwidth, svgheight])
		.padding(2)
		(root)

	//console.log(root.leaves())
	
	
	// add on mouse over popup
	
	$(".tooltip" ).remove();	
	var tooltip = d3.select("body")
		.append("div")
			.attr("class", "tooltip")
			.style ("width","200px")
			.style ("height","100px")
			.style ("opacity","1") //counter default
	
	  // use this information to add rectangles:
	  svg
		.selectAll("rect")
		.data(root.leaves())
		.enter()
		.append("rect")
		  .attr('x', function (d) { return d.x0; })
		  .attr('y', function (d) { return d.y0; })
		  .attr('width', function (d) { return d.x1 - d.x0; })
		  .attr('height', function (d) { return d.y1 - d.y0; })
		  //.style("stroke", "#263f53")
		  //.style("fill", "#428bca")
		  .style("fill", function(d) {	
		  		//console.log(d.data.group);
				if(d.data.group == 'PHILIPS')
				{
				  result = "#4e79a7";
				 }
				 else if(d.data.group == 'EDWARDS')
				 {
				  result = "#e15759";
				 }		
				 else if(d.data.group == 'NASPERS')
				 {
				  result = "#76b7b2";
				 }
				 else if(d.data.group == 'PORTICUS')
				 {
				  result = "#af7aa1";
				 }		  
				 else if(d.data.group == 'ABFS')
				 {
				  result = "#edc949";
				 }	
				 else if(d.data.group == 'INBEV')
				 {
				  result = "#af7aa1";
				 }		  
				 else if(d.data.group == 'INGB')
				 {
				  result = "#f28e2c";
				 }	
				 else if(d.data.group == 'VIPHOR')
				 {
				  result = "#80b1d3";
				 }	
		  		 else
				 {
				  result = "#AAA";
				 }		
		  
			return result;
	  })
	
	
	//.on("mouseover", function(event,d){tooltip.text(d.data.originator); return tooltip.style("visibility", "visible");})
	 .on("mouseover", function(event,d){
		  return tooltip.style("visibility", "visible");})

	 //.on("mouseover", function(event,d) {
		

	.on("mousemove", function(event,d){
		  tooltip.html( "<ul class=\"list-group\"><li class=\"list-group-item active\">"+ d.data.client +"<br/>" + d.data.clientcode + "</li><li class=\"list-group-item\">" + d.data.billedhours + " billed hours</li><li class=\"list-group-item\">" + d.data.jobs + " jobs</li><li class=\"list-group-item\">" + d.data.newslides + " new slides</li><li class=\"list-group-item\">"+ d.data.editedslides + " edited slides</li><li class=\"list-group-item\">"+ d.data.additionalhours + " hours</li></ul>");

	  return tooltip
		  .style("top",function(d){ 
			if (event.pageY<svgheight) {
				return(event.pageY+10)+"px";
			} else{
				return(event.pageY-210)+"px";
			}
			})
		   .style("left",function(d){ 
				if (event.pageX<svgwidth/2) {
					return(event.pageX+10)+"px";
				} else{
					return(event.pageX-230)+"px";
				}
		   })
	  })

	  .on("mouseout", function(){return tooltip.style("visibility", "hidden");});
	
	// add the text labels
	 svg
		.selectAll("text")
		.data(root.leaves())
		.enter()
		.filter (function(d) { return (d.x1 - d.x0) > 40}) //filter out rectanmgles with a width under 100
		 .append("text")
		   .attr("x", function(d){ return d.x0+3})    // +10 to adjust position (more right)
		  .attr("y", function(d){ return d.y0+12})    // +20 to adjust position (lower)
		  .text(function(d){ return d.data.clientcode})
		  .attr("font-size", "10px")
		  .attr("font-family", "arial narrow")
		  .attr("fill", "white")
	

    /* */
	
	}




Number.prototype.formatdecimals = function(c, d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };

Number.prototype.formatintegers = function(d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : 0, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(0) : "");
 };