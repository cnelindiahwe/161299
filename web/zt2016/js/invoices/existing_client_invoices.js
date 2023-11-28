$(function(){
 		
 		//loading
 		$('#table_loading_message').hide() ;
 		
 		//client dropdown
 		$('#client_dropdown_selector_submit').hide();
		$("#client_dropdown_selector").change ( function () {
				$("#client_dropdown_form").submit();
			}
		); 	

		//confirm client billing guidelines submit
		$('#client-billing-guidelines-form').submit(function(e) {
	        var currentForm = this;
	        e.preventDefault();
	        
			var msg= "Update client billing guidelines?";	
				bootbox.confirm(msg, function(result) {
				    if (result) {
				        currentForm.submit();
				    } 
				}); //bootbox.confirm	        
		});	


			
		//table
	
		$.fn.dataTable.moment( 'D-MMM-YYYY' );
	
        $('#client_invoices_table').css({ "display": "table", "width": "100%"}).dataTable( {
        "dom":' <"col-sm-6"f><"col-sm-6"i>t<"bottom10"p><"bottom20"l>r<"clear">',
        //"dom":'difrtp',
        "order": [[ 2, "desc" ]],
        //"scrollY":        "20em",
        "scrollCollapse": true,
        "paging":         false,
        "info":           true,
        "processing": true,
        "orderClasses": false,
       // "lengthMenu": [[10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "All"]],
		"aoColumnDefs" : [ {
		    "bSortable" : false,
		    "aTargets" : [ "no-sort" ]
		} ],
               
    
           
        /*BELOWFROM HERE:
         * https://datatables.net/examples/api/multi_filter_select.html*/
        initComplete: function () {

        	var icount=0
            this.api().columns([0,1,2,3,5]).every( function () {
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
 
 				if (column.index()!=1 && column.index()!=2 && column.index()!=3 && column.index()!=6) {
	                column.data().unique().sort().each( function ( d, j ) {
	                	
	                	if (d.substring(0, 7)=="<a href") {
		                	//http://stackoverflow.com/questions/960156/regex-in-javascript-to-remove-links
		               		d=d.replace(/<a\b[^>]*>/i,"").replace(/<\/a>/i, "");
		               		}
	                    select.append( '<option value="'+d+'">'+d+'</option>' )
	                } ); //column.data().unique().sort().each
                } 
                
                else if (column.index()==1 || column.index()==2 || column.index()==3){ //column.index()!=2 && column.index()!=3
 			        var dates = [];
                	column.data().each(function (d, j) {
                		
                		
                		if (d.length == 11) {
                			 d= d.substring(7,11)
                		} else{
                			d= d.substring(6,10)
                		} 

						if (dates.indexOf(d) < 0) {
			                dates.push(d);
			                select.append('<option value="' + d + '">' + d + '</option>');
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
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 4 ).footer() ).html(
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