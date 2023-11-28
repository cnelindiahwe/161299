$(function(){
		//$('.datepicker').datepicker()
		$('#table_loading_message').hide() ;
		
	
		$.fn.dataTable.moment( 'D-MMM-YYYY' );

        $('#invoices_table').css({ "display": "table", "width": "100%"}).dataTable( {
        "dom":' <"col-sm-6"f><"col-sm-6"i>t<"bottom10"p><"bottom20"l>r<"clear">',
        //"dom":'difrtp',
        "order": [[ 2, "desc" ]],
        "scrollY":        "20em",
        "scrollCollapse": true,
        "paging":         false,
        "info":           true,
        "processing": true,
        "orderClasses": false,
        "lengthMenu": [[10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "All"]],
        columnDefs: [ {
            targets: [ 3 ],
            orderData: [ 0, 1 ]
        }, {
            targets: [ 1 ],
            orderData: [ 1, 0 ]
        } ],
               
    
           
        /*BELOWFROM HERE:
         * https://datatables.net/examples/api/multi_filter_select.html*/
        initComplete: function () {
			$('#invoices_table_wrapper').css({ "display": "block"}).show();
			var icount=0
            this.api().columns([2,3,4,6,7]).every( function () {
                var column = this;
                var select = $('<select class=""><option value=""></option></select>')
                   .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                         	column
                             	.search( val ? '.*'+val+'.*' : '', true, false )
                            	.draw();
                    } );
 
 				if (column.index()!=2 && column.index()!=3 && column.index()!=4) {
	                column.data().unique().sort().each( function ( d, j ) {
	                	
	                	if (d.substring(0, 7)=="<a href") {
		                	//http://stackoverflow.com/questions/960156/regex-in-javascript-to-remove-links
		               		d=d.replace(/<a\b[^>]*>/i,"").replace(/<\/a>/i, "");
		               		}
	                    select.append( '<option value="'+d+'">'+d+'</option>' )
	                } ); //column.data().unique().sort().each
                } 
                
                else { //column.index()!=2 && column.index()!=3
 			        var dates = [];
                	column.data().each(function (d, j) {
                		
                		if (d.length == 1) {
                			//leave as is
                		} 
                		else if (d.length == 11) {
                			 d= d.substring(3,11)
                		} 
                		else {
                			d= d.substring(2,10)
                		} 
                		
						if (d.length > 1) {
							if (dates.indexOf(d) < 0) {
				                dates.push(d);
				                select.append('<option value="' + d + '">' + d + '</option>');
				            }    
                		}

	                } ); //column.data().unique().sort().each
                }//if //column.index()!=2 && column.index()!=3
                
                icount=icount+1   
            } );

			
       }, //initComplete: 

         "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            /*total = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 )
 			*/
            // Total over this page
            pageTotal = api
                .column( 5, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 5 ).footer() ).html(
                pageTotal.formatMoney(2)  //+ ' ('+ total.formatMoney(2) +')'
            );
        }
        
        
    }); //$('#invoices_table').dataTable



});   //$(function()     


Number.prototype.formatMoney = function(c, d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };