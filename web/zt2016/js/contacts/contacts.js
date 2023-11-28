$(function(){
 	  $('#table_loading_message').hide() ;
      $('#contacts_table').css({ "display": "table", "width": "100%"}).dataTable( {
        "dom":' <"col-sm-6"f><"col-sm-6"i>t<"bottom10"p><"bottom20"l>r<"clear">',
        //"sDom": "t",
       "order": [[ 2, "Desc" ]],
         "scrollY":        "20em",
        "scrollCollapse": true,
        "paging":         false,
        "info":           true,
        "processing": true,
        "orderClasses": false,
			columnDefs: [
			  { targets: 'no-sort', orderable: false }
			],


           
        //BELOWFROM HERE:
        // https://datatables.net/examples/api/multi_filter_select.html
        initComplete: function () {

        	var icount=0
            this.api().columns([1,2,3,4]).every( function () {
                var column = this;
                var select = $('<select class="form-control"><option value=""></option></select>')
                   .appendTo( $(column.footer()).empty() )
                   .on( 'change', function () {
                   		//reset all but changed
                   		
					    // $('th select').not(this).each(function(){
					    //     $(this).prop('selectedIndex',0);
					   // });
                        var val = $.fn.dataTable.util.escapeRegex( $(this).val());
                        //var choice = $(this).prop('selectedIndex')
                       // table.columns().search( '' );
                   		//$('th select').prop('selectedIndex',0);
                   		//$(this).prop('selectedIndex',choice);
                     	column
                         	.search( val ? '.*'+val+'.*' : '', true, false )
                        	.draw();
                   } ); //.on( 'change', function ()
                    

		        var dates = [];
            	column.data().unique().sort().each(function (d, j) {
            		
            		if (d.substring(0, 7)=="<a href") {
	                	//http://stackoverflow.com/questions/960156/regex-in-javascript-to-remove-links
	               		d=d.replace(/<a\b[^>]*>/i,"").replace(/<\/a>/i, "");
	               		 select.append('<option value="' + d + '">' + d + '</option>');
	               	} else if (dates.indexOf(d) < 0) {
		                dates.push(d);
		                select.append('<option value="' + d + '">' + d + '</option>');
		            }    
				} ); //column.data().unique().sort().each
            	
           } ); //this.api().columns([0,1,2,3,4]).every
   		}//$('#invoices_table').dataTable
       
        
    }); //$('#invoices_table').dataTable



});   //$(function()     


Number.prototype.formatNumber = function(c, d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };
 
 /**/